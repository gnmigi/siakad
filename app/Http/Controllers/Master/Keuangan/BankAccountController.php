<?php

namespace App\Http\Controllers\Master\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\BankAccount;
use App\Models\Pengaturan\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BankAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['menus'] = "Master";
        $data['pages'] = "Rekening Bank";
        $data['academy'] = $data['webs']->school_apps . ' oleh ' . $data['webs']->school_name;
        $data['spref'] = $user ? $user->prefix : '';
        $data['user'] = $user;
        
        $data['bankAccounts'] = BankAccount::orderBy('is_primary', 'desc')->orderBy('bank_name')->get();
        
        return view('master.keuangan.bank-account-index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:10',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'virtual_account_prefix' => 'nullable|string|max:10',
        ]);

        try {
            // If this is set as primary, unset other primary accounts
            if ($request->has('is_primary')) {
                BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            }

            BankAccount::create([
                'code' => strtoupper($request->bank_code . '_' . substr($request->account_number, -4)),
                'bank_name' => $request->bank_name,
                'bank_code' => strtoupper($request->bank_code),
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'branch' => $request->branch,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'is_primary' => $request->has('is_primary'),
                'virtual_account_prefix' => $request->virtual_account_prefix,
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Rekening bank berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan rekening bank: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $code)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:10',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'virtual_account_prefix' => 'nullable|string|max:10',
        ]);

        try {
            $bankAccount = BankAccount::where('code', $code)->firstOrFail();
            
            // If this is set as primary, unset other primary accounts
            if ($request->has('is_primary') && !$bankAccount->is_primary) {
                BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            }
            
            $bankAccount->update([
                'bank_name' => $request->bank_name,
                'bank_code' => strtoupper($request->bank_code),
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'branch' => $request->branch,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'is_primary' => $request->has('is_primary'),
                'virtual_account_prefix' => $request->virtual_account_prefix,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Rekening bank berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui rekening bank: ' . $e->getMessage());
        }
    }

    public function destroy($code)
    {
        try {
            $bankAccount = BankAccount::where('code', $code)->firstOrFail();
            
            // Check if bank account is being used
            if ($bankAccount->paymentTransactions()->exists()) {
                return redirect()->back()->with('error', 'Rekening bank tidak dapat dihapus karena sedang digunakan');
            }
            
            $bankAccount->update(['deleted_by' => Auth::id()]);
            $bankAccount->delete();

            return redirect()->back()->with('success', 'Rekening bank berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus rekening bank: ' . $e->getMessage());
        }
    }

    public function toggleStatus($code)
    {
        try {
            $bankAccount = BankAccount::where('code', $code)->firstOrFail();
            
            $bankAccount->update([
                'is_active' => !$bankAccount->is_active,
                'updated_by' => Auth::id(),
            ]);

            $status = $bankAccount->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Rekening bank berhasil {$status}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function setPrimary($code)
    {
        try {
            // Validate code parameter
            if (empty($code)) {
                return redirect()->back()->with('error', 'Kode rekening tidak valid');
            }

            // Find the bank account
            $bankAccount = BankAccount::where('code', $code)->first();
            
            if (!$bankAccount) {
                return redirect()->back()->with('error', 'Rekening bank tidak ditemukan');
            }

            // Check if account is already primary
            if ($bankAccount->is_primary) {
                return redirect()->back()->with('info', 'Rekening ini sudah menjadi rekening utama');
            }

            // Check if account is active
            if (!$bankAccount->is_active) {
                return redirect()->back()->with('error', 'Tidak dapat menjadikan rekening nonaktif sebagai rekening utama');
            }

            // Unset all primary accounts
            BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            
            // Set this account as primary
            $bankAccount->update([
                'is_primary' => true,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Rekening utama berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah rekening utama: ' . $e->getMessage());
        }
    }
}
