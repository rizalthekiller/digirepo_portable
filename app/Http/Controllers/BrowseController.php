<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\Faculty;
use App\Models\ThesisType;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = Thesis::with('user.department.faculty')->where('status', 'approved');

        // Filter by Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        // Filter by Faculty
        if ($request->filled('faculty')) {
            $query->whereHas('user.department.faculty', function($q) use ($request) {
                $q->where('id', $request->faculty);
            });
        }

        // Filter by Department (Prodi)
        if ($request->filled('department')) {
            $query->whereHas('user.department', function($q) use ($request) {
                $q->where('id', $request->department);
            });
        }

        // Filter by Author (User Name)
        if ($request->filled('author')) {
            $author = $request->author;
            $query->whereHas('user', function($q) use ($author) {
                $q->where('name', 'like', "%{$author}%");
            });
        }

        // Filter by Supervisor
        if ($request->filled('supervisor')) {
            $supervisor = $request->supervisor;
            $query->where('supervisor_name', 'like', "%{$supervisor}%");
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Sorting Logic
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest': $query->orderBy('year', 'asc'); break;
            case 'title': $query->orderBy('title', 'asc'); break;
            case 'newest': 
            default: $query->latest(); break;
        }

        $theses = $query->paginate(12)->withQueryString();
        $faculties = Faculty::all();
        $departments = \App\Models\Department::orderBy('name')->get();
        $types = ThesisType::all();
        $years = Thesis::where('status', 'approved')->select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('browse.index', compact('theses', 'faculties', 'departments', 'years', 'types'));
    }
}
