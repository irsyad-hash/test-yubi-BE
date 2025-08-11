<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrderService
{
    public function createSalesOrderWithDetails(array $salesOrderData, array $detailsData)
    {
        return DB::transaction(function () use ($salesOrderData, $detailsData) {
            try {
                Log::info('Creating SalesOrder with data:', $salesOrderData);
                $salesOrder = SalesOrder::create($salesOrderData);
                
                Log::info('SalesOrder created with ID:', ['id' => $salesOrder->id]);
                foreach ($detailsData as $detail) {
                    $detail['sales_order_id'] = $salesOrder->id;
                    Log::info('Creating SalesOrderDetail:', $detail);
                    SalesOrderDetail::create($detail);
                }

                Log::info('All SalesOrderDetails created successfully');
                return $salesOrder->load('details');
                
            } catch (\Exception $e) {
                Log::error('Error in createSalesOrderWithDetails:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                throw $e;
            }
        });
    }

    public function getAllSalesOrders()
    {
        try {
            return SalesOrder::with('details')->get();
        } catch (\Exception $e) {
            Log::error('Error in getAllSalesOrders:', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getSalesOrderById($id)
    {
        try {
            return SalesOrder::with('details')->find($id);
        } catch (\Exception $e) {
            Log::error('Error in getSalesOrderById:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateSalesOrderWithDetails($id, array $salesOrderData, array $detailsData)
    {
        return DB::transaction(function () use ($id, $salesOrderData, $detailsData) {
            try {
                Log::info('Updating SalesOrder with ID:', ['id' => $id, 'data' => $salesOrderData]);
                
                $salesOrder = SalesOrder::find($id);
                if (!$salesOrder) {
                    return null;
                }

                $salesOrder->update($salesOrderData);
                
                Log::info('SalesOrder updated successfully');
                $existingDetailIds = $salesOrder->details->pluck('id')->toArray();
                $updatedDetailIds = [];

                foreach ($detailsData as $detail) {
                    $detail['sales_order_id'] = $salesOrder->id;
                    
                    if (isset($detail['id']) && $detail['id']) {
                        $existingDetail = SalesOrderDetail::find($detail['id']);
                        if ($existingDetail && $existingDetail->sales_order_id == $salesOrder->id) {
                            $existingDetail->update($detail);
                            $updatedDetailIds[] = $detail['id'];
                            Log::info('Updated existing SalesOrderDetail:', ['id' => $detail['id']]);
                        }
                    } else {
                        unset($detail['id']);
                        $newDetail = SalesOrderDetail::create($detail);
                        $updatedDetailIds[] = $newDetail->id;
                        Log::info('Created new SalesOrderDetail:', ['id' => $newDetail->id]);
                    }
                }

                $detailsToDelete = array_diff($existingDetailIds, $updatedDetailIds);
                if (!empty($detailsToDelete)) {
                    SalesOrderDetail::whereIn('id', $detailsToDelete)
                        ->where('sales_order_id', $salesOrder->id)
                        ->delete();
                    Log::info('Deleted SalesOrderDetails:', ['ids' => $detailsToDelete]);
                }

                Log::info('All SalesOrderDetails updated successfully');
                return $salesOrder->fresh(['details']);
                
            } catch (\Exception $e) {
                Log::error('Error in updateSalesOrderWithDetails:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                throw $e;
            }
        });
    }

    public function deleteSalesOrder($id)
    {
        return DB::transaction(function () use ($id) {
            try {
                Log::info('Deleting SalesOrder with ID:', ['id' => $id]);
                
                $salesOrder = SalesOrder::find($id);
                if (!$salesOrder) {
                    return false;
                }
                $salesOrder->details()->delete();
                Log::info('SalesOrderDetails deleted');
                $salesOrder->delete();
                Log::info('SalesOrder deleted successfully');

                return true;
                
            } catch (\Exception $e) {
                Log::error('Error in deleteSalesOrder:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                throw $e;
            }
        });
    }
}