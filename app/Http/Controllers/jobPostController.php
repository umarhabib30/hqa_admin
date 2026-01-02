<?php

namespace App\Http\Controllers;

use App\Models\jobPost as  JobPost;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index()
    {
        $jobPosts = JobPost::latest()->get();
        return view('dashboard.career.jobpost.index', compact('jobPosts'));
    }

    public function create()
    {
        return view('dashboard.career.jobpost.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_category' => 'required|string|max:255',
            'job_location' => 'required|string|max:255',
        ]);

        JobPost::create($request->all());

        return redirect()->route('jobPost.index')
            ->with('success', 'Job post created successfully');
    }

    public function edit(JobPost $jobPost)
    {
        return view('dashboard.career.jobpost.update', compact('jobPost'));
    }

    public function update(Request $request, JobPost $jobPost)
    {
        $request->validate([
            'job_category' => 'required|string|max:255',
            'job_location' => 'required|string|max:255',
        ]);

        $jobPost->update($request->all());

        return redirect()->route('jobPost.index')
            ->with('success', 'Job post updated successfully');
    }

    public function destroy(JobPost $jobPost)
    {
        $jobPost->delete();

        return redirect()->route('jobPost.index')
            ->with('success', 'Job post deleted successfully');
    }
}
