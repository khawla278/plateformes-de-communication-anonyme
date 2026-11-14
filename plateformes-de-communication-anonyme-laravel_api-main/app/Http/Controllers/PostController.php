<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // 🔹 Liste tous les posts avec leurs relations
    public function index()
    {
        $posts = Post::with(['user', 'categorie', 'commentaires.user', 'reactions'])
            ->latest()
            ->get();

        return response()->json($posts);
    }

    // 🔹 Afficher un post spécifique
    public function show($id)
    {
        $post = Post::with(['user', 'categorie', 'commentaires.user', 'reactions'])
            ->findOrFail($id);

        return response()->json($post);
    }

    // 🔹 Créer un nouveau post
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'categorie_id' => $request->categorie_id,
            'user_id' => $request->user()->id,
            'image' => $imagePath
        ]);

        return response()->json([
            'message' => 'Post créé avec succès',
            'post' => $post
        ], 201);
    }

    // 🔹 Mettre à jour un post
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // 🔒 Sécurité : seul l'auteur ou admin peut modifier
        if ($post->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'contenu' => 'sometimes|required|string',
            'categorie_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Supprimer ancienne image si existe
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $post->image = $request->file('image')->store('posts', 'public');
        }

        $post->update($request->only(['titre', 'contenu', 'categorie_id', 'image']));

        return response()->json([
            'message' => 'Post mis à jour',
            'post' => $post
        ]);
    }

    // 🔹 Supprimer un post
    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // 🔒 Sécurité : seul l'auteur ou admin peut supprimer
        if ($post->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Supprimer image si existe
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json(['message' => 'Post supprimé']);
    }



public function deleteByUser(Request $request, $userId, $postId)
{
    $authUser = $request->user();

    if (!in_array($authUser->role, ['admin', 'moderateur'])) {
        return response()->json(['error' => 'Accès refusé'], 403);
    }

    $post = Post::where('id', $postId)
        ->where('user_id', $userId)
        ->first();

    if (!$post) {
        return response()->json(['error' => 'Post non trouvé'], 404);
    }

    // 🔥 NOTIFICATION
    Notification::create([
        'user_id' => $userId,
        'message' => 'Votre post a été supprimé par un admin/modérateur'
    ]);

    $post->delete();

    return response()->json([
        'message' => 'Post supprimé'
    ]);
}


}