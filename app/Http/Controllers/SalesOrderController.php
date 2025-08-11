<?php

namespace App\Http\Controllers;

use App\Services\SalesOrderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SalesOrderController extends Controller
{
    protected $salesOrderService;

    public function __construct(SalesOrderService $salesOrderService)
    {
        $this->salesOrderService = $salesOrderService;
    }
    public function store(Request $request)
    {
        try {
            Log::info('Sales Order Request received:', $request->all());

            $validated = $request->validate([
                'po_buyer_no' => 'required|string',
                'order_type_id' => 'required|integer',
                'order_date' => 'required|date',
                'shipping_date' => 'required|date',
                'customer_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'exchange_rate' => 'required|numeric',
                'pph' => 'required|integer',
                'status_id' => 'required|integer',
                'vat' => 'required|boolean',
                'buyer_address' => 'required|string',
                'sub_amount' => 'required|numeric',
                'total_discount' => 'required|numeric',
                'after_discount' => 'required|numeric',
                'total_vat' => 'required|numeric',
                'total_pph' => 'required|numeric',
                'grand_total' => 'required|numeric',
                'details' => 'required|array',
                'details.*.ref_type_id' => 'required|integer',
                'details.*.ref_num' => 'required|integer',
                'details.*.item_type_id' => 'required|integer',
                'details.*.product_code' => 'required|string',
                'details.*.product_name' => 'required|string',
                'details.*.unit_type_id' => 'required|integer',
                'details.*.price' => 'required|numeric',
                'details.*.quantity' => 'required|integer',
                'details.*.discount_amount' => 'required|numeric',
                'details.*.discount_percent' => 'required|numeric',
                'details.*.total_amount' => 'required|numeric',
                'details.*.remark' => 'nullable|string',
            ]);

            Log::info('Validation passed');

            $salesOrderData = collect($validated)->except('details')->toArray();
            $detailsData = $validated['details'];

            Log::info('Sales Order Data:', $salesOrderData);
            Log::info('Details Data:', $detailsData);

            $salesOrder = $this->salesOrderService->createSalesOrderWithDetails($salesOrderData, $detailsData);

            Log::info('Sales Order created successfully');

            return response()->json([
                'message' => 'Sales order created successfully',
                'data' => $salesOrder
            ], 201);

        } catch (ValidationException $e) {
            Log::error('Validation Error:', $e->errors());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Sales Order Creation Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error creating sales order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            return response()->json($this->salesOrderService->getAllSalesOrders());
        } catch (\Exception $e) {
            Log::error('Get All Sales Orders Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error retrieving data', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $salesOrder = $this->salesOrderService->getSalesOrderById($id);
            if (!$salesOrder) {
                return response()->json(['message' => 'Data not found'], 404);
            }
            return response()->json(['data' => $salesOrder]);
        } catch (\Exception $e) {
            Log::error('Get Sales Order by ID Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error retrieving data', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('Sales Order Update Request received:', ['id' => $id, 'data' => $request->all()]);

            $validated = $request->validate([
                'po_buyer_no' => 'required|string',
                'order_type_id' => 'required|integer',
                'order_date' => 'required|date',
                'shipping_date' => 'required|date',
                'customer_id' => 'required|integer',
                'currency_id' => 'required|integer',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'exchange_rate' => 'required|numeric',
                'pph' => 'required|integer',
                'status_id' => 'required|integer',
                'vat' => 'required|boolean',
                'buyer_address' => 'required|string',
                'sub_amount' => 'required|numeric',
                'total_discount' => 'required|numeric',
                'after_discount' => 'required|numeric',
                'total_vat' => 'required|numeric',
                'total_pph' => 'required|numeric',
                'grand_total' => 'required|numeric',
                'details' => 'required|array',
                'details.*.id' => 'nullable|integer',
                'details.*.ref_type_id' => 'required|integer',
                'details.*.ref_num' => 'required|integer',
                'details.*.item_type_id' => 'required|integer',
                'details.*.product_code' => 'required|string',
                'details.*.product_name' => 'required|string',
                'details.*.unit_type_id' => 'required|integer',
                'details.*.price' => 'required|numeric',
                'details.*.quantity' => 'required|integer',
                'details.*.discount_amount' => 'required|numeric',
                'details.*.discount_percent' => 'required|numeric',
                'details.*.total_amount' => 'required|numeric',
                'details.*.remark' => 'nullable|string',
            ]);

            Log::info('Update validation passed');

            $salesOrderData = collect($validated)->except('details')->toArray();
            $detailsData = $validated['details'];

            $salesOrder = $this->salesOrderService->updateSalesOrderWithDetails($id, $salesOrderData, $detailsData);

            if (!$salesOrder) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            Log::info('Sales Order updated successfully');

            return response()->json([
                'message' => 'Sales order updated successfully',
                'data' => $salesOrder
            ], 200);

        } catch (ValidationException $e) {
            Log::error('Update Validation Error:', $e->errors());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Sales Order Update Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error updating sales order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('Sales Order Delete Request received:', ['id' => $id]);

            $result = $this->salesOrderService->deleteSalesOrder($id);

            if (!$result) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            Log::info('Sales Order deleted successfully');

            return response()->json([
                'message' => 'Sales order deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Sales Order Delete Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error deleting sales order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}