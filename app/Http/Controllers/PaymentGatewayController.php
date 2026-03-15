<?php

namespace App\Http\Controllers;

use App\Mail\ApiKeyChangedNotification;
use App\Models\AdminSetting;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PaymentGatewayController extends Controller
{
    /**
     * Display payment gateway settings.
     * GET /payment-gateways
     */
    public function index()
    {
        $user = Auth::user();
        if ($user && $user->isSuperAdmin()) {
            $gateways = PaymentGateway::whereNull('user_id')->get();
        } else {
            $globalGateways = PaymentGateway::whereNull('user_id')->where('is_active', true)->get();
            $userGateways = $user ? $user->paymentGateways()->get()->keyBy('name') : collect();
            
            $gateways = $globalGateways->map(function ($gateway) use ($userGateways) {
                $userGateway = $userGateways->get($gateway->name);
                
                $g = new PaymentGateway();
                $g->id = $gateway->id;
                $g->name = $gateway->name;
                $g->display_name = $gateway->display_name;
                $g->description = $gateway->description;
                $g->is_active = $gateway->is_active; // global status
                $g->api_key = $userGateway ? $userGateway->api_key : '';
                $g->webhook_url = $userGateway ? $userGateway->webhook_url : '';
                
                return $g;
            });
        }

        return view('dashboard.payment-gateways.index', [
            'gateways' => $gateways,
        ]);
    }

    /**
     * Update payment gateway settings.
     * POST /payment-gateways/{name}/update
     */
    public function update(Request $request, $name)
    {
        $user = Auth::user();
        abort_if($user->isSuperAdmin(), 403, 'Super Admins view global toggles, not keys.');
        
        $validated = $request->validate([
            'api_key' => 'required|string|min:10',
            'webhook_url' => 'nullable|url',
        ]);

        $gateway = PaymentGateway::firstOrNew([
            'user_id' => $user->id,
            'name' => $name,
        ]);
        
        if (!$gateway->exists) {
            $global = PaymentGateway::whereNull('user_id')->where('name', $name)->firstOrFail();
            $gateway->display_name = $global->display_name;
            $gateway->description = $global->description;
            $gateway->is_active = true;
        }

        $gateway->api_key = $validated['api_key'];
        $gateway->webhook_url = $validated['webhook_url'] ?? null;

        if ($gateway->isDirty('api_key') && $gateway->exists) {
            $adminEmail = AdminSetting::get('admin_email');
            if ($adminEmail) {
                try {
                    Mail::to($adminEmail)->send(new ApiKeyChangedNotification($gateway, $request->ip()));
                } catch (\Exception $e) {
                    \Log::warning('API key change notification email failed: ' . $e->getMessage());
                }
            }
        }

        $gateway->save();

        return redirect()->back()
            ->with('success', $gateway->display_name.' API credentials saved successfully!');
    }

    /**
     * Toggle gateway active status.
     * POST /payment-gateways/{gateway}/toggle
     */
    public function toggle(PaymentGateway $gateway)
    {
        abort_unless(Auth::user()->isSuperAdmin() && is_null($gateway->user_id), 403, 'Only Super Admins can toggle global gateways.');
        $gateway->update(['is_active' => ! $gateway->is_active]);

        $status = $gateway->is_active ? 'enabled' : 'disabled';

        return redirect()->back()
            ->with('success', $gateway->display_name.' has been '.$status.'.');
    }
}
