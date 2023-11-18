<?php
// Recupere os dados da ficha de treino da sessão
session_start();
$ficha_treino = json_decode($_POST['ficha_treino'], true);

// Inclua a biblioteca PhpSpreadsheet
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

// Verifique se há dados para exportar
if (isset($_POST['exportar']) && isset($ficha_treino['divisao'])) {
    // Criar uma instância do PhpSpreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Adicionar cabeçalhos dinâmicos para cada divisão
    $col = 1;
    foreach ($ficha_treino['divisao'] as $divisao => $exercicios) {
        $sheet->setCellValueByColumnAndRow($col, 1, "Treino $divisao");
        $sheet->setCellValueByColumnAndRow($col, 2, 'Exercícios');
        $sheet->setCellValueByColumnAndRow($col + 1, 2, 'Séries');
        $sheet->setCellValueByColumnAndRow($col + 2, 2, 'Repetições');

        $row = 3;
        foreach ($exercicios as $exercicio) {
            $sheet->setCellValueByColumnAndRow($col, $row, $exercicio['nome_exercicio']);
            $sheet->setCellValueByColumnAndRow($col + 1, $row, $exercicio['serie']);
            $sheet->setCellValueByColumnAndRow($col + 2, $row, $exercicio['repeticao']);
            $row++;
        }

        // Avançar para a próxima divisão
        $col += 3; // Cada divisão possui 3 colunas (Exercício, Série, Repetições)
    }

    // Definir cabeçalhos HTTP para download do arquivo
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="ficha_treino.xls"');
    header('Cache-Control: max-age=0');

    // Criar o Writer para Excel5 (xls)
    $writer = new Xls($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    // Caso não haja dados para exportar, redirecione ou exiba uma mensagem de erro
    echo "Nenhum dado para exportar.";
}
