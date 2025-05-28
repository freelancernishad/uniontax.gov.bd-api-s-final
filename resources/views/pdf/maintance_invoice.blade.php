<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Fee Invoice</title>
    <style>
        body {
            font-family: 'bangla', sans-serif;
            font-size: 12px;
            color: #000;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
        }

        .invoice-title {
            text-align: center;
            background-color: #003366;
            color: white;
            font-size: 18px;
            padding: 5px;
            margin: 15px 0;
            width: 120px;
            margin-left: auto;
            margin-right: auto;
            border-radius: 6px;
        }

        .details-table,
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 6px;
            font-size: 12px;
        }

        .bill-box,
        .invoice-meta {
            border: 1px solid #003366;
            padding: 8px;
        }

        .invoice-table th {
            background-color: #003366;
            color: white;
            border: 1px solid #003366;
            padding: 6px;
            text-align: center;
        }

        .invoice-table td {
            border: 1px solid #003366;
            padding: 6px;
            text-align: center;
        }

        .summary td {
            text-align: right;
            padding-right: 10px;
            font-weight: bold;
        }

        .in-word {
            font-style: italic;
            font-weight: bold;
        }

        .footer-message {
            background-color: #003366;
            color: white;
            padding: 6px;
            font-size: 12px;
            margin-top: 10px;
        }

        .bank-info {
            margin-top: 10px;
            font-size: 13px;
        }

        .bank-info b {
            font-size: 14px;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>




<div class="invoice-title">INVOICE</div>

<table class="details-table">
    <tr>
        <td class="bill-box" style="width: 50%;">
            <strong>BILL TO:</strong><br>
            NISHAD HOSSAIN<br>
            DEBIGANJ,PANCHAGARH
        </td>
        <td class="invoice-meta" style="width: 50%;">
            <table style="width:100%;">
                <tr>
                    <td><strong>INVOICE NO.</strong></td>
                    <td style="text-align:right;">{{ \Carbon\Carbon::parse($fee->payment_date)->format('Y').'-' . str_pad($fee->id, 4, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td><strong>DATE</strong></td>
                    <td style="text-align:right;">{{ \Carbon\Carbon::parse($fee->payment_date)->format('d/m/Y') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br>

<table class="invoice-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>DESCRIPTION</th>
            <th>QTY</th>
            <th>UNIT PRICE</th>
            <th>AMOUNT</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>01.</td>
            <td>Maintenance Fee for {{ $fee->period }}</td>
            <td>01</td>
            <td>{{ number_format($fee->amount, 2) }}</td>
            <td>{{ number_format($fee->amount, 2) }}</td>
        </tr>
        <tr class="summary">
            <td colspan="4">SUB TOTAL =</td>
            <td>{{ number_format($fee->amount, 2) }}</td>
        </tr>
        <tr class="summary">
            <td colspan="4">Transaction Fee (1.5%) =</td>
            <td>{{ number_format($fee->transaction_fee, 2) }}</td>
        </tr>
        <tr class="summary">
            <td colspan="4">VAT =</td>
            <td>0.00</td>
        </tr>
        <tr class="summary">
            <td colspan="4">IT =</td>
            <td>0.00</td>
        </tr>
        <tr class="summary">
            <td colspan="4">TOTAL =</td>
            <td>{{ number_format($fee->amount + $fee->transaction_fee, 2) }}</td>
        </tr>
    </tbody>
</table>

<p class="in-word">
    In word: {{ $inWords }}.
</p>

<div class="footer-message">
    Thanks for choosing us. <em>Stay with www.softwebsys.com</em>
</div>

    <table width="100%" style="margin-top: 50px;">
        <tr>
            <td width="70%" >
            </td>
            <td style="text-align: center;">
                <div style="display: inline-block; text-align: center; margin-top: 20px;">
                    With best regards,<br><br>
                    <strong>Nishad</strong><br/>
                    <strong>Director/Manager</strong><br/>
                    <strong>Softweb System Solution</strong><br/>
                    <strong>Debiganj, Panchagarh</strong>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
