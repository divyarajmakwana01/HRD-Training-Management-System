<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;




class ProgrammeController extends Controller
{



    public function index()
    {
        try {
            $programmes = DB::table('programme')->get();
            $coordinators = DB::table('coordinators')->get();

            return view('admin.programme', compact('programmes', 'coordinators'));
        } catch (\Exception $e) {
            Log::error("Error fetching programmes or coordinators: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading data. Check logs.');
        }
    }

    public function getParticipantDashboard()
    {
        try {
            // Fetch counts for each programme type
            $webinars = DB::table('programme')->where('programmeType', 1)->count();
            $userAwareness = DB::table('programme')->where('programmeType', 2)->count();
            $workshops = DB::table('programme')->where('programmeType', 3)->count();
            $trainings = DB::table('programme')->where('programmeType', 4)->count();
            $collaborative = DB::table('programme')->where('programmeType', 5)->count();

            // Fetch total participants
            $participants = DB::table('participants')->count();

            // Debug logs
            Log::info("Dashboard Stats:", [
                'Webinars' => $webinars,
                'User Awareness' => $userAwareness,
                'Workshops' => $workshops,
                'Trainings' => $trainings,
                'Collaborative' => $collaborative,
                'Participants' => $participants
            ]);

            return view('participants.dashboard', compact(
                'webinars',
                'userAwareness',
                'workshops',
                'trainings',
                'collaborative',
                'participants'
            ));
        } catch (\Exception $e) {
            Log::error("Error fetching programme counts: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading data. Check logs.');
        }
    }






    public function store(Request $request)
    {
        try {
            Log::info("Starting to store new programme data.");

            // Log the request data to debug the issue
            Log::info("Request data for storing programme: ", $request->all());

            // Validate the request
            $validatedData = $request->validate([
                'year' => 'required|integer',
                'name' => 'required|string|max:255',
                'programmeType' => 'required|in:1,2,3,4,5',
                'programmeBrief' => 'nullable|string',
                'brochure_link' => 'nullable|url', // It's optional and must be a valid URL if provided
                'programmeVenue' => 'nullable|string|max:255',
                'questionnaire' => 'nullable|string',
                'startdate' => 'required|date',
                'enddate' => 'required|date|after_or_equal:startdate',
                'startTime' => 'nullable|string',
                'endTime' => 'nullable|string',
                'fees' => 'nullable|string|max:20',
                'fees_with_acc' => 'nullable|string|max:20',
                'fees_exemption' => 'required|in:0,1', // Ensure it's 0 or 1
            ]);

            Log::info("Validation successful for new programme.", $validatedData);

            // Convert time format properly
            $validatedData['startTime'] = $request->startTime ? date('H:i', strtotime($request->startTime)) : null;
            $validatedData['endTime'] = $request->endTime ? date('H:i', strtotime($request->endTime)) : null;

            Log::info("Formatted startTime: " . $validatedData['startTime']);
            Log::info("Formatted endTime: " . $validatedData['endTime']);

            // Insert new programme data into the database
            $inserted = DB::table('programme')->insert([
                'year' => $validatedData['year'],
                'name' => $validatedData['name'],
                'programmeType' => $validatedData['programmeType'],
                'programmeBrief' => $validatedData['programmeBrief'],
                'brochure_link' => $validatedData['brochure_link'] ?? null, // Set default to null if not provided
                'programmeVenue' => $validatedData['programmeVenue'],
                'questionnaire' => $validatedData['questionnaire'],
                'startdate' => $validatedData['startdate'],
                'enddate' => $validatedData['enddate'],
                'startTime' => $validatedData['startTime'],
                'endTime' => $validatedData['endTime'],
                'fees' => $validatedData['fees'],
                'fees_with_acc' => $validatedData['fees_with_acc'],
                'fees_exemption' => $validatedData['fees_exemption'],
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info("Programme data inserted into the database.", [
                'programme_name' => $validatedData['name'],
                'year' => $validatedData['year'],
                'programmeType' => $validatedData['programmeType']
            ]);

            if ($inserted) {
                Log::info("Programme created successfully.");
                return redirect()->route('admin.programme')->with('success', 'Programme created successfully!');
            } else {
                Log::warning("Failed to create programme. No rows inserted.");
                return redirect()->back()->with('error', 'An error occurred while creating the programme.');
            }
        } catch (\Exception $e) {
            Log::error("Error while storing programme data.", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while storing the programme. Please check logs.');
        }
    }



    public function edit($id)
    {
        $programme = DB::table('programme')->where('id', $id)->first();
        $coordinators = DB::table('coordinators')->get();

        if (!$programme) {
            return redirect()->route('admin.programme')->with('error', 'Programme not found!');
        }

        return view('admin.programme.edit', compact('programme', 'coordinators'));
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info("Starting update for programme ID: {$id}");

            // Validate the request
            $validatedData = $request->validate([
                'year' => 'required|integer',
                'name' => 'required|string|max:255',
                'programmeType' => 'required|in:1,2,3,4,5',
                'programmeBrief' => 'nullable|string',
                'brochure_link' => 'nullable|url',
                'programmeVenue' => 'nullable|string|max:255',
                'questionnaire' => 'nullable|string',
                'startdate' => 'required|date',
                'enddate' => 'required|date|after_or_equal:startdate',
                'startTime' => 'nullable|string', // Accept as string first
                'endTime' => 'nullable|string',   // Accept as string first
                'fees' => 'nullable|string|max:20',
                'fees_with_acc' => 'nullable|string|max:20',
                'fees_exemption' => 'required|in:0,1',
            ]);

            Log::info("Validation successful for programme ID: {$id}", $validatedData);

            // Convert time format properly
            $validatedData['startTime'] = $request->startTime ? date('H:i', strtotime($request->startTime)) : null;
            $validatedData['endTime'] = $request->endTime ? date('H:i', strtotime($request->endTime)) : null;

            Log::info("Formatted startTime: " . $validatedData['startTime']);
            Log::info("Formatted endTime: " . $validatedData['endTime']);

            // Check if programme exists
            $programme = DB::table('programme')->where('id', $id)->first();
            if (!$programme) {
                Log::error("Programme ID {$id} not found.");
                return redirect()->back()->with('error', 'Programme not found.');
            }

            // Convert programmeType to string
            $validatedData['programmeType'] = (string) $validatedData['programmeType'];

            // Perform the update
            $updated = DB::table('programme')
                ->where('id', $id)
                ->update([
                    'year' => $validatedData['year'],
                    'name' => $validatedData['name'],
                    'programmeType' => $validatedData['programmeType'],
                    'programmeBrief' => $validatedData['programmeBrief'],
                    'brochure_link' => $validatedData['brochure_link'],
                    'programmeVenue' => $validatedData['programmeVenue'],
                    'questionnaire' => $validatedData['questionnaire'],
                    'startdate' => $validatedData['startdate'],
                    'enddate' => $validatedData['enddate'],
                    'startTime' => $validatedData['startTime'],
                    'endTime' => $validatedData['endTime'],
                    'fees' => $validatedData['fees'],
                    'fees_with_acc' => $validatedData['fees_with_acc'],
                    'fees_exemption' => $validatedData['fees_exemption'],
                    'updated_at' => now(),
                    'updatedby' => auth()->user()->name ?? 'System',
                ]);

            Log::info("Update query executed for programme ID: {$id}, Result: {$updated}");

            if ($updated) {
                return redirect()->route('admin.programme')->with('success', 'Programme updated successfully!');
            } else {
                Log::warning("No changes detected for programme ID: {$id}");
                return redirect()->back()->with('error', 'No changes were made.');
            }
        } catch (\Exception $e) {
            Log::error("Error updating programme ID: {$id}", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while updating the programme. Please check logs.');
        }
    }



    public function destroy($id)
    {
        $deleted = DB::table('programme')->where('id', $id)->delete();

        if ($deleted) {
            return redirect()->route('admin.programme')->with('success', 'Programme deleted successfully!');
        } else {
            return redirect()->route('admin.programme')->with('error', 'Programme not found or could not be deleted.');
        }
    }

    public function addCoordinator(Request $request, $programme_id)
    {
        $coordinator_ids = $request->input('coordinator_id');

        if (!is_array($coordinator_ids)) {
            $coordinator_ids = [$coordinator_ids];
        }

        foreach ($coordinator_ids as $coordinator_id) {
            DB::table('programme_coordinators')->insert([
                'programme_id' => $programme_id,
                'coordinator_id' => $coordinator_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Coordinators assigned successfully!');
    }


    public function toggleStatus($id)
    {
        try {
            Log::info("Starting to toggle status for programme ID: {$id}");

            // Check if the programme exists
            $programme = DB::table('programme')->where('id', $id)->first();

            if (!$programme) {
                Log::error("Programme ID {$id} not found.");
                return redirect()->back()->with('error', 'Programme not found.');
            }

            // Toggle the 'active' status
            $newStatus = $programme->active == 1 ? 0 : 1;

            // Update the status in the database
            DB::table('programme')
                ->where('id', $id)
                ->update(['active' => $newStatus, 'updated_at' => now()]);

            Log::info("Status for programme ID {$id} updated to: {$newStatus}");

            return redirect()->route('admin.programme')->with('success', 'Programme status updated successfully!');
        } catch (\Exception $e) {
            Log::error("Error toggling status for programme ID: {$id}", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while toggling the programme status.');
        }
    }

    public function destroyCoordinatorsProgramme($programme_id, $coordinator_id)
    {
        // Remove the coordinator from the programme
        $deleted = DB::table('programme_coordinators')
            ->where('programme_id', $programme_id)
            ->where('coordinator_id', $coordinator_id)
            ->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Coordinator removed from programme successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to remove coordinator from programme.');
        }

    }


}
