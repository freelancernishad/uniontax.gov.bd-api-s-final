<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        @page {
            margin: 10px;
        }
        .memoborder {
            width: 48%;
        }
        .memo {
            background: white;
        }
        .memoHead {
            text-align: center;
            color: black;
        }
        .companiname {
            margin: 0;
        }
        p {
            color: black;
            margin: 0;
        }
        .thead .tr {
            color: black;
        }
        .thead .tr .th {
            color: black;
        }
        .tr {
            border: 1px solid black;
        }
        .th {
            border: 1px solid black;
            border-right: 1px solid white;
        }
        .td {
            border: 1px solid black;
        }
        .table, .td {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;
            color: black;
        }
        .tdlist {
            height: 200px;
            vertical-align: top;
        }
        .slNo {
            float: right;
            width: 300px;
        }
    </style>
</head>
<body>
    <div id="body">





        {!! HoldingTaxInvoiceBody($unions, $HoldingBokeya, $customers, $previousamount, $currentamount, $payment, $amount_text,$totalAmount,'left') !!}
        {!! HoldingTaxInvoiceBody($unions, $HoldingBokeya, $customers, $previousamount, $currentamount, $payment, $amount_text,$totalAmount,'right') !!}





    </div>
</body>
</html>
