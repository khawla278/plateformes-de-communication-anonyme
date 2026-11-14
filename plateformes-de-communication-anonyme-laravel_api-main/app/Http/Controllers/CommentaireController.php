<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commentaire;
use App\Models\Post;
use App\Models\Notification;

class CommentaireController extends Controller
{
    // 🔹 Liste des commentaires d’un post
    public function index($post_id)
    {
        $commentaires = Commentaire::with('user')
            ->where('post_id', $post_id)
            ->latest()
            ->get();

        return response()->json($commentaires);
    }

    // 🔹 Ajouter un commentaire
    public function store(Request $request, $post_id)
    {
        $request->validate([
            'contenu' => 'required|string'
        ]);

        $user = $request->user();
        $post = Post::findOrFail($post_id);

        $commentaire = Commentaire::create([
            'contenu' => $request->contenu,
            'user_id' => $user->id,
            'post_id' => $post_id
        ]);

        // ✅ TRÈS IMPORTANT : charger l'utilisateur
        $commentaire->load('user');

        // 🔔 Notification si ce n’est pas l’auteur du post
        if ($post->user_id !== $user->id) {
            Notification::create([
                'user_id' => $post->user_id,
                'from_user' => $user->pseudo,
                'post_title' => $post->titre,
                'type' => 'comment',
                'message' => 'a commenté votre post'
            ]);
        }

        return response()->json([
            'message' => 'Commentaire ajouté',
            'commentaire' => $commentaire
        ]);
    }

    // 🔹 Modifier un commentaire
    public function update(Request $request, $id)
    {
        $commentaire = Commentaire::findOrFail($id);

        if ($commentaire->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'contenu' => 'required|string'
        ]);

        $commentaire->update([
            'contenu' => $request->contenu
        ]);

        return response()->json([
            'message' => 'Commentaire modifié',
            'commentaire' => $commentaire
        ]);
    }

    // 🔹 Supprimer un commentaire
    public function destroy(Request $request, $id)
    {
        $commentaire = Commentaire::findOrFail($id);

        if (
            $commentaire->user_id !== $request->user()->id &&
            $request->user()->role !== 'admin'
        ) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $commentaire->delete();

        return response()->json([
            'message' => 'Commentaire supprimé'
        ]);
    }
}