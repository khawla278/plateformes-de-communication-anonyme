<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use App\Models\Commentaire;
use App\Models\Reaction;

class UserController extends Controller
{
    // 🔹 Afficher tous les utilisateurs (ADMIN)
    public function index()
    {
        return User::all();
    }

    // 🔹 Afficher profil utilisateur connecté
    public function profile(Request $request)
    {
        return $request->user();
    }

    // 🔹 Modifier profil
    public function update(Request $request)
    {
        $user = $request->user();

        $user->update([
            'pseudo' => $request->pseudo,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Profil mis à jour', 'user' => $user]);
    }

      public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6',
        'password_confirmation' => 'required'
    ]);

    if ($request->password !== $request->password_confirmation) {
        return response()->json([
            'message' => 'Passwords do not match'
        ], 422);
    }

    $user = $request->user();

    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json([
        'message' => 'Mot de passe mis à jour'
    ]);
}

    // 🔹 Supprimer utilisateur (ADMIN)
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    // 🔹 Changer rôle (ADMIN)
    public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->role = $request->role; // admin, moderateur, utilisateur
        $user->save();

        return response()->json(['message' => 'Rôle modifié']);
    }

    // 🔹 Suspendre utilisateur (MODERATEUR ou ADMIN)
    public function suspend($id)
    {
        $user = User::findOrFail($id);

        $user->status = 'suspendu';
        $user->save();

        return response()->json(['message' => 'Utilisateur suspendu']);
    }

// 🔹 Lister uniquement les modérateurs (ADMIN seulement)
public function listModerateurs()
{
    $userAuth = auth()->user();

    if ($userAuth->role !== 'admin') {
        return response()->json([
            'error' => 'Accès refusé (Admin uniquement)'
        ], 403);
    }

    $moderateurs = User::where('role', 'moderateur')->get();

    return response()->json([
        'count' => $moderateurs->count(),
        'moderateurs' => $moderateurs
    ]);
}

// 🔹 Historique de l'utilisateur (commentaires + likes)
public function history(Request $request)
{
    $user = $request->user();

    // 🗨️ Mes commentaires
    $comments = Commentaire::with('post')
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    // 👍 Mes likes
    $likes = Reaction::with('post')
        ->where('user_id', $user->id)
        ->where('type', 'like')
        ->latest()
        ->get();

    return response()->json([
        'comments' => $comments,
        'likes' => $likes
    ]);
}






    
    
}