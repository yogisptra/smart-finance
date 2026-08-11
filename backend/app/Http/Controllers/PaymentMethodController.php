<?php
namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $paymentMethods = PaymentMethod::where('user_id', $request->user()->id)
            ->orWhereNull('user_id') // For defaults if we choose to make them user_id=null
            ->paginate($request->per_page ?? 20);
            
        return response()->json([
            'success' => true,
            'message' => 'Payment methods retrieved successfully',
            'data' => $paymentMethods->items(),
            'meta' => [
                'current_page' => $paymentMethods->currentPage(),
                'per_page' => $paymentMethods->perPage(),
                'total' => $paymentMethods->total(),
                'last_page' => $paymentMethods->lastPage(),
            ]
        ]);
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $paymentMethod = $request->user()->paymentMethods()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Payment method created successfully',
            'data' => $paymentMethod
        ], 201);
    }

    public function show(Request $request, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id && $paymentMethod->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment method retrieved successfully',
            'data' => $paymentMethod
        ]);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or default'], 403);
        }

        $paymentMethod->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Payment method updated successfully',
            'data' => $paymentMethod
        ]);
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or default'], 403);
        }

        $paymentMethod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment method deleted successfully',
            'data' => null
        ], 204);
    }
}
