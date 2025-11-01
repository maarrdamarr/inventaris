<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();

        $query->when(request()->filled('role_id'), function ($q) {
            return $q->whereRelation('roles', 'id', '=', request('role_id'));
        });

        $users = $query->get()->except(auth()->id());
        $roles = Role::withCount('users')->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $role = Role::findById($validated['role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
        $user->assignRole($role);

        return to_route('pengguna.index')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $role = Role::findById($validated['role_id']);

        $credentials = [];
        if ($validated['password'] !== null) {
            $credentials['password'] = bcrypt($validated['password']);
        } else {
            $credentials = collect($validated)->except('password', 'password_confirmation')->toArray();
        }

        $user->update($credentials);
        $user->syncRoles($role);

        return redirect()->route('pengguna.index')->with('success', 'Data berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return to_route('pengguna.index')->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Import students from CSV (name,email[,password])
     */
    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $role = Role::where('name', 'Siswa')->first();
        if (!$role) {
            return back()->with('error', 'Role Siswa belum tersedia. Jalankan seeder atau buat role terlebih dahulu.');
        }

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Tidak dapat membaca file CSV.');
        }

        $created = 0; $skipped = 0; $invalid = 0;
        $rowIndex = 0;
        // Attempt to detect header
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rowIndex++;
            if ($rowIndex === 1) {
                $lower = array_map(fn($v) => strtolower(trim($v)), $row);
                if (in_array('name', $lower) && in_array('email', $lower)) {
                    // header row detected; continue to next
                    continue;
                }
                // else treat as data row
            }
            if (count($row) < 2) { $invalid++; continue; }
            [$name, $email, $password] = [$row[0] ?? null, $row[1] ?? null, $row[2] ?? null];
            $name = trim((string)$name); $email = trim((string)$email);
            if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $invalid++; continue; }
            if (User::where('email', $email)->exists()) { $skipped++; continue; }
            $pwd = $password ? trim((string)$password) : 'password';
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($pwd),
            ]);
            $user->assignRole($role);
            $created++;
        }
        fclose($handle);

        $msg = "Import selesai: {$created} berhasil, {$skipped} dilewati (email duplikat), {$invalid} tidak valid.";
        return back()->with('success', $msg);
    }
}
