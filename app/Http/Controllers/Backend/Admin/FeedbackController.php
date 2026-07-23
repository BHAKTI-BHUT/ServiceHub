<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Feedback::with(['booking', 'customer'])->orderBy('id', 'desc');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('booking_no', fn($row) => $row->booking ? $row->booking->booking_number : 'N/A')
                ->addColumn('customer_name', fn($row) => $row->customer ? $row->customer->name : 'N/A')
                ->addColumn('rating_stars', function($row) {
                    $stars = '';
                    for($i=1; $i<=5; $i++) {
                        if($i <= $row->rating) $stars .= '<i class="ri-star-fill text-warning"></i>';
                        else $stars .= '<i class="ri-star-line text-muted"></i>';
                    }
                    return $stars;
                })
                ->addColumn('label', function($row) {
                    if($row->rating >= 4) return '<span class="badge bg-success">Excellent</span>';
                    if($row->rating == 3) return '<span class="badge bg-warning">Average</span>';
                    return '<span class="badge bg-danger">Poor</span>';
                })
                ->addColumn('action', function($row) {
                    $deleteForm = '<form action="' . route('admin.feedback.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $deleteForm . '</div>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
                ->rawColumns(['rating_stars', 'label', 'action'])
                ->make(true);
        }
        return view('Backend.Admin.Feedback.Index');
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $customerEmail = $feedback->customer ? $feedback->customer->email : null;
        $feedbackReview = $feedback->review;
        $feedbackRating = $feedback->rating;

        // Delete from local admin database
        $feedback->delete();

        // Delete from CodeIgniter website database using direct PDO
        $websiteDeleteMsg = '';
        try {
            // Robust credential fetching: config() works with cached config, env() works without cache
            // If both fail, parse .env file directly as ultimate fallback
            $host     = config('database.connections.website.host') ?: env('WEBSITE_DB_HOST', 'localhost');
            $dbname   = config('database.connections.website.database') ?: env('WEBSITE_DB_DATABASE');
            $username = config('database.connections.website.username') ?: env('WEBSITE_DB_USERNAME');
            $password = config('database.connections.website.password') ?? env('WEBSITE_DB_PASSWORD', '');

            // Ultimate fallback: read .env file directly if config/env both return empty
            if (empty($dbname)) {
                $envPath = base_path('.env');
                if (file_exists($envPath)) {
                    $envContent = file_get_contents($envPath);
                    preg_match('/^WEBSITE_DB_HOST=(.*)$/m', $envContent, $hMatch);
                    preg_match('/^WEBSITE_DB_DATABASE=(.*)$/m', $envContent, $dMatch);
                    preg_match('/^WEBSITE_DB_USERNAME=(.*)$/m', $envContent, $uMatch);
                    preg_match('/^WEBSITE_DB_PASSWORD=(.*)$/m', $envContent, $pMatch);
                    $host     = isset($hMatch[1]) ? trim($hMatch[1]) : 'localhost';
                    $dbname   = isset($dMatch[1]) ? trim($dMatch[1]) : '';
                    $username = isset($uMatch[1]) ? trim($uMatch[1]) : '';
                    $password = isset($pMatch[1]) ? trim($pMatch[1]) : '';
                }
            }

            if (empty($dbname)) {
                throw new \Exception('Website database name not configured. Add WEBSITE_DB_DATABASE to .env file.');
            }

            $pdo = new \PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            $deleted = false;

            // Try by email first (most reliable match)
            if ($customerEmail) {
                $stmt = $pdo->prepare("DELETE FROM reviews WHERE email = ? LIMIT 1");
                $stmt->execute([$customerEmail]);
                if ($stmt->rowCount() > 0) {
                    $deleted = true;
                }
            }

            // If no email match, try by review content + rating
            if (!$deleted && $feedbackReview) {
                $stmt = $pdo->prepare("DELETE FROM reviews WHERE r_desc = ? AND stars = ? LIMIT 1");
                $stmt->execute([$feedbackReview, $feedbackRating]);
                if ($stmt->rowCount() > 0) {
                    $deleted = true;
                }
            }

            $pdo = null; // Close connection

            $websiteDeleteMsg = $deleted
                ? 'Website review also deleted.'
                : 'No matching review found on website to delete.';

        } catch (\Throwable $e) {
            \Log::error("Failed to delete review from Website DB: " . $e->getMessage());
            $websiteDeleteMsg = 'Could not delete from website: ' . $e->getMessage();
        }

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully. ' . $websiteDeleteMsg
        ]);
    }
}
