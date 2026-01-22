<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Upload Bacs Report</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input[type="file"], select { width: 100%; padding: 8px; }
        .notice { background: #f3f4f6; padding: 12px; border-radius: 6px; margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Upload Bacs Report</h1>

    @if (!empty($uploaded))
        <div class="notice">
            <strong>Report queued.</strong> Report ID: {{ $reportId }}
        </div>
    @endif

    @if ($errors->any())
        <div class="notice" style="color:#b91c1c;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bacs.upload') }}" enctype="multipart/form-data">
        @csrf
        <label for="merchant_id">Merchant</label>
        <select id="merchant_id" name="merchant_id" required>
            @foreach ($merchants as $merchant)
                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
            @endforeach
        </select>

        <label for="type">Report type</label>
        <select id="type" name="type">
            <option value="ARUDD">ARUDD</option>
            <option value="ADDACS">ADDACS</option>
        </select>

        <label for="report_file">Report file (CSV or JSON)</label>
        <input id="report_file" name="report_file" type="file" required>

        <div style="margin-top: 16px;">
            <button type="submit">Upload report</button>
        </div>
    </form>
</body>
</html>
