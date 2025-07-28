<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class StructureController extends Controller
{
    public function showStructure(Request $request)
    {
        try {
            $symbol = $request->query('symbol', 'ABB');
            $fromDate = $request->query('from_date') ?? Carbon::now()->subMonth()->format('Y-m-d');
            $toDate = $request->query('to_date') ?? Carbon::now()->format('Y-m-d');

            // Normalized performance
            $normalizedPerformance = Http::get('http://103.97.127.42:8001/overview/normalized-performance', [
                'symbol' => $symbol,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ])->json();

            // Get all symbols
            $allSymbols = Http::get('http://103.97.127.42:8001/overview/all-symbols')->json();

            // Get latest price change with symbol in path
            $getSymbols = Http::get("http://103.97.127.42:8001/overview/latest-price-change/{$symbol}")->json();

            // Get latest-ohlcv
            $latestOhlcv = Http::get("http://103.97.127.42:8001/overview/latest-ohlcv/{$symbol}")->json();

            // Latest financial metrics
            $latestFinancialMetric = Http::get("http://103.97.127.42:8001/overview/latest-financial-metrics/{$symbol}", [
                'report_type' => 'quarterly',
            ])->json();

            // Structure data
            $incomeStatementQuarterlyStructure = Http::get('http://103.97.127.42:8001/ratio/income-statement-structure', [
                'symbol' => $symbol,
                'report_type' => 'quarterly',
            ])->json();
            $incomeStatementYearlyStructure = Http::get('http://103.97.127.42:8001/ratio/income-statement-structure', [
                'symbol' => $symbol,
                'report_type' => 'yearly',
            ])->json();

            $balanceYearlyStructure = Http::get('http://103.97.127.42:8001/ratio/balance-sheet-structure', [
                'symbol' => $symbol,
                'report_type' => 'yearly',
            ])->json();

            $balanceQuarterlyStructure = Http::get('http://103.97.127.42:8001/ratio/balance-sheet-structure', [
                'symbol' => $symbol,
                'report_type' => 'quarterly',
            ])->json();

            $cashflowYearlyStructure = Http::get('http://103.97.127.42:8001/ratio/cash-flow-structure', [
                'symbol' => $symbol,
                'report_type' => 'yearly',
            ])->json();

            $cashflowQuarterlyStructure = Http::get('http://103.97.127.42:8001/ratio/cash-flow-structure', [
                'symbol' => $symbol,
                'report_type' => 'quarterly',
            ])->json();

            // Fundamental data
            $ratioFundamental = Http::get('http://103.97.127.42:8001/fundamental/ratio', [
                'symbol' => $symbol,
                'report_type' => 'quarterly',
            ])->json() ?? [];

            $balanceSheetFundamental = Http::get('http://103.97.127.42:8001/fundamental/balance-sheet', [
                'symbol' => $symbol,
                'report_type' => 'quarterly',
            ])->json();

            $incomeStatementFundamental = Http::get('http://103.97.127.42:8001/fundamental/income-statement', [
                'symbol' => $symbol,
                'report_type' => 'quarterly',
            ])->json() ?? [];

            // Cashflow Fundamental Data
            $cashflowFundamental = Http::get('http://103.97.127.42:8001/fundamental/cashflow', [
                'symbol' => $symbol,
                'report_type' => 'quarterly',
                'method' => 'direct',
            ])->json() ?? [];

            // Truyền sang view
            return view('show_structure', [
                'symbol' => $symbol,
                'cashflowYearlyStructure' => $cashflowYearlyStructure,
                'cashflowQuarterlyStructure' => $cashflowQuarterlyStructure,
                'balanceYearlyStructure' => $balanceYearlyStructure,
                'balanceQuarterlyStructure' => $balanceQuarterlyStructure,
                'incomeStatementYearlyStructure' => $incomeStatementYearlyStructure,
                'incomeStatementQuarterlyStructure' => $incomeStatementQuarterlyStructure,
                'balanceSheetFundamental' => $balanceSheetFundamental,
                'ratioFundamental' => $ratioFundamental,
                'incomeStatementFundamental' => $incomeStatementFundamental,
                'cashflowFundamental' => $cashflowFundamental,
                'normalizedPerformance' => $normalizedPerformance,
                'allSymbols' => $allSymbols,
                'getSymbols' => $getSymbols,
                'latestOhlcv' => $latestOhlcv,
                'latestFinancialMetric' => $latestFinancialMetric, // Thêm vào view
            ]);

        } catch (\Throwable $e) {
            Log::error('Lỗi khi gọi API', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Trả về view rỗng nếu lỗi
            return view('show_structure', [
                'symbol' => $symbol,
                'cashflowYearlyStructure' => [],
                'cashflowQuarterlyStructure' => [],
                'balanceYearlyStructure' => [],
                'balanceQuarterlyStructure' => [],
                'incomeStatementYearlyStructure' => [],
                'incomeStatementQuarterlyStructure' => [],
                'balanceSheetFundamental' => [],
                'ratioFundamental' => [],
                'incomeStatementFundamental' => [],
                'cashflowFundamental' => [],
                'normalizedPerformance' => [],
                'allSymbols' => [],
                'getSymbols' => [],
                'latestOhlcv' => [],
                'latestFinancialMetric' => [], // Thêm vào view rỗng nếu lỗi
            ]);
        }
    }

    public function getNormalizedPerformance(Request $request)
    {
        try {
            $symbol = $request->query('symbol', 'ABB');
            $fromDate = $request->query('from_date') ?? Carbon::now()->subMonth()->format('Y-m-d');
            $toDate = $request->query('to_date') ?? Carbon::now()->format('Y-m-d');

            $normalizedPerformance = Http::get('http://103.97.127.42:8001/overview/normalized-performance', [
                'symbol' => $symbol,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ])->json();

            return response()->json($normalizedPerformance);

        } catch (\Throwable $e) {
            Log::error('Lỗi fetch normalizedPerformance:', [
                'message' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Lỗi khi gọi normalizedPerformance'], 500);
        }
    }
    public function getlatestFinancialMetrics(Request $request)
{
    try {
        $symbol = $request->query('symbol', 'ABB');
        $reportType = $request->query('report_type', 'yearly');

        $response = Http::get('http://103.97.127.42:8001/overview/latest-financial-metrics', [
            'symbol' => $symbol,
            'report_type' => $reportType,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Không thể lấy dữ liệu từ API bên ngoài. Status: ' . $response->status());
        }

        $latestFinancialMetrics = $response->json();

        if (empty($latestFinancialMetrics) || !is_array($latestFinancialMetrics)) {
            throw new \Exception('Dữ liệu tài chính không hợp lệ hoặc rỗng');
        }

        return response()->json($latestFinancialMetrics);
    } catch (\Throwable $e) {
        Log::error('Lỗi fetch latestFinancialMetrics:', [
            'message' => $e->getMessage(),
            'symbol' => $request->query('symbol', 'ABB'),
            'report_type' => $request->query('report_type', 'yearly'),
        ]);
        return response()->json(['error' => 'Lỗi khi lấy dữ liệu tài chính: ' . $e->getMessage()], 500);
    }
}
}