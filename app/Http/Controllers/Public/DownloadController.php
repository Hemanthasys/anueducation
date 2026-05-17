<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Download;

class DownloadController extends Controller
{
    public function index()
    {
        // Load all active downloads for client-side filtering
            $downloads = Download::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($download) {
                // Extract Google Drive file ID for preview
                $driveId = null;
                if ($download->drive_url) {
                    preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $download->drive_url, $matches);
                    $driveId = $matches[1] ?? null;
                }

                return [
                    'id'             => $download->id,
                    'title_en'       => $download->title_en,
                    'title_si'       => $download->title_si,
                    'category'       => $download->category,
                    'year'           => $download->year,
                    'file_path'      => $download->file_path,
                    'file_url'       => $download->file_path ? \Storage::url($download->file_path) : null,
                    'file_ext'       => $download->file_path ? strtolower(pathinfo($download->file_path, PATHINFO_EXTENSION)) : null,
                    'drive_url'      => $download->drive_url,
                    'drive_id'       => $driveId,
                    'download_count' => $download->download_count ?? 0,
                    'is_drive'       => !empty($download->drive_url),
                ];
            });

        // Get unique years for filter dropdown
        $years = Download::where('is_active', true)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('public.downloads.index', compact('downloads', 'years'));
    }
}