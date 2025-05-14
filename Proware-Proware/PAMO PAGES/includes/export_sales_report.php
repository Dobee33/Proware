<?php
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$conn = mysqli_connect("localhost", "root", "", "proware");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$startDate = isset($_GET['startDate']) ? trim($_GET['startDate']) : '';
$endDate = isset($_GET['endDate']) ? trim($_GET['endDate']) : '';

$where = [];
if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where[] = "(s.transaction_number LIKE '%$s%' OR s.item_code LIKE '%$s%' OR i.item_name LIKE '%$s%')";
}
if ($startDate) {
    $where[] = "DATE(s.sale_date) >= '" . mysqli_real_escape_string($conn, $startDate) . "'";
}
if ($endDate) {
    $where[] = "DATE(s.sale_date) <= '" . mysqli_real_escape_string($conn, $endDate) . "'";
}
$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT s.transaction_number, s.item_code, i.item_name, s.size, s.quantity, s.price_per_item, s.total_amount, s.sale_date
        FROM sales s
        LEFT JOIN inventory i ON s.item_code = i.item_code
        $where_clause
        ORDER BY s.sale_date DESC";

$result = mysqli_query($conn, $sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header
$headers = ['Order Number', 'Item Code', 'Item Name', 'Size', 'Quantity', 'Price Per Item', 'Total Amount', 'Sale Date'];
$sheet->fromArray($headers, NULL, 'A1');

// Make header row bold
$sheet->getStyle('A1:H1')->getFont()->setBold(true);

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->fromArray([
        $row['transaction_number'],
        $row['item_code'],
        $row['item_name'],
        $row['size'],
        $row['quantity'],
        $row['price_per_item'],
        $row['total_amount'],
        $row['sale_date']
    ], NULL, 'A' . $rowNum);
    $rowNum++;
}

// Center-align Quantity (E), Price Per Item (F), and Total Amount (G)
$lastDataRow = $rowNum - 1;
$sheet->getStyle('E2:E' . $lastDataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F2:F' . $lastDataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('G2:G' . $lastDataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Auto-size columns
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 