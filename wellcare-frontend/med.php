<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// This is a simplified example. In a real application, you would:
// 1. Connect to a database
// 2. Sanitize inputs
// 3. Handle errors properly

$drug1 = isset($_GET['drug1']) ? $_GET['drug1'] : '';
$drug2 = isset($_GET['drug2']) ? $_GET['drug2'] : '';
$includePrice = isset($_GET['includePrice']) ? $_GET['includePrice'] : false;

// Sample database - in real app this would come from a DB
$drugDatabase = [
    'بنادول' => [
        'activeIngredient' => 'باراسيتامول (500mg)',
        'uses' => 'تسكين الآلام وخفض الحرارة',
        'contraindications' => 'الحساسية للباراسيتامول، أمراض الكبد الشديدة',
        'sideEffects' => 'نادراً ما يسبب طفح جلدي، غثيان',
        'interactions' => 'يزيد خطر تلف الكبد مع الكحول، قد يتفاعل مع الوارفارين',
        'price' => '15 ريال'
    ],
    'فيفادول' => [
        'activeIngredient' => 'باراسيتامول (500mg)',
        'uses' => 'تسكين الآلام وخفض الحرارة',
        'contraindications' => 'الحساسية للباراسيتامول، أمراض الكبد',
        'sideEffects' => 'نادراً ما يسبب اضطرابات في الجهاز الهضمي',
        'interactions' => 'يزيد خطر تلف الكبد مع الكحول',
        'price' => '12 ريال'
    ],
    // Add more drugs as needed
];

// Get drug data
$drug1Data = isset($drugDatabase[$drug1]) ? $drugDatabase[$drug1] : getUnknownDrugData();
$drug2Data = isset($drugDatabase[$drug2]) ? $drugDatabase[$drug2] : getUnknownDrugData();

// Remove price if not requested
if (!$includePrice) {
    unset($drug1Data['price']);
    unset($drug2Data['price']);
}

// Prepare response
$response = [
    'success' => true,
    'drug1' => $drug1Data,
    'drug2' => $drug2Data
];

echo json_encode($response);

function getUnknownDrugData() {
    return [
        'activeIngredient' => 'غير معروف',
        'uses' => 'غير معروف',
        'contraindications' => 'غير معروف',
        'sideEffects' => 'غير معروف',
        'interactions' => 'غير معروف',
        'price' => 'غير متوفر'
    ];
}
?>