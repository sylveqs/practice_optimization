<?php
/**
 * УЧЕБНЫЙ ПРИМЕР: Оптимизация поиска в массиве
 *
 * ЗАДАЧА:
 * У нас есть список из 50 000 "товаров" (чисел).
 * Нам нужно найти конкретный товар и проверить, есть ли он в списке.
 */

// ---------------------------------------------------------
// 1. ГЕНЕРАЦИЯ ДАННЫХ
// ---------------------------------------------------------

function generateProducts(int $count): array {
    $products = [];

    for ($i = 0; $i < $count; $i++) {
        $products[] = [
            'id' => $i,
            'name' => 'Product_' . $i,
            'price' => rand(100, 10000)
        ];
    }

    return $products;
}

// ---------------------------------------------------------
// ВАРИАНТ 1: МЕДЛЕННЫЙ КОД
// ---------------------------------------------------------

function searchProductSlow($products, $searchId) {
    // Линейный поиск O(N)
    foreach ($products as $product) {
        if ($product['id'] == $searchId) {
            return $product;
        }
    }
    return null;
}

// ---------------------------------------------------------
// ВАРИАНТ 2: БЫСТРЫЙ КОД
// ---------------------------------------------------------

function searchProductFast($products, $searchId) {
    /*
     * Оптимизация:
     * создаём ассоциативный массив:
     * ключ = id
     * значение = товар
     *
     * поиск через isset() работает за O(1)
     */

    static $indexedProducts = null;

    // создаём индекс только один раз
    if ($indexedProducts === null) {
        foreach ($products as $product) {
            $indexedProducts[$product['id']] = $product;
        }
    }

    return isset($indexedProducts[$searchId])
        ? $indexedProducts[$searchId]
        : null;
}

// ---------------------------------------------------------
// ФУНКЦИЯ БЕНЧМАРКА
// ---------------------------------------------------------

function runBenchmark($functionName, $data, $searchId, $iterations = 1000) {
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $functionName($data, $searchId);
    }

    $end = microtime(true);

    $totalTime = $end - $start;
    $avgTime = ($totalTime / $iterations) * 1000;

    return [
        'total_time_sec' => $totalTime,
        'avg_time_ms' => $avgTime,
        'iterations' => $iterations
    ];
}

// ---------------------------------------------------------
// ЗАПУСК ТЕСТОВ
// ---------------------------------------------------------

$products = generateProducts(50000);
$searchId = 49999;
$iterations = 1000;

$slowResult = runBenchmark(
    'searchProductSlow',
    $products,
    $searchId,
    $iterations
);

$fastResult = runBenchmark(
    'searchProductFast',
    $products,
    $searchId,
    $iterations
);

// вычисляем ускорение
$speedup = $slowResult['total_time_sec'] / $fastResult['total_time_sec'];

// ---------------------------------------------------------
// ВЫВОД
// ---------------------------------------------------------

echo "=== РЕЗУЛЬТАТЫ БЕНЧМАРКА ({$iterations} итераций) ===\n\n";

echo "Тест: searchProductSlow\n";
echo "  Общее время: " . number_format($slowResult['total_time_sec'], 4) . " сек\n";
echo "  Среднее время: " . number_format($slowResult['avg_time_ms'], 4) . " мс\n\n";

echo "Тест: searchProductFast\n";
echo "  Общее время: " . number_format($fastResult['total_time_sec'], 4) . " сек\n";
echo "  Среднее время: " . number_format($fastResult['avg_time_ms'], 4) . " мс\n\n";

echo ">>> Ускорение: " . number_format($speedup, 2) . " раз\n";