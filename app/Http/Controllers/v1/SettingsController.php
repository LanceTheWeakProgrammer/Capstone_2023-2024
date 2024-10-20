<?php

namespace App\Http\Controllers\v1;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\AppInfo;
use App\Models\ContactInfo;
use App\Models\TeamInfo; 
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function indexApp()
    {
        try {
            $app_info = AppInfo::all();

            return response()->json([
                'success' => true,
                'data' => $app_info
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve app information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateApp(Request $request) 
    {
        $validate = $request->validate([
            'appID' => 'required|exists:app_info,appID', 
            'appTitle' => 'required|string|max:255',
            'appAbbout' => 'nullable|string' 
        ]);

        try {
            $app_info = AppInfo::findOrFail($validate['appID']);
            $app_info->update($validate);

            return response()->json([
                'success' => true,
                'message' => 'App information updated successfully.',
                'data' => $app_info
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update app information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexContacts()
    {
        try {
            $contact_info = ContactInfo::all();

            return response()->json([
                'success' => true,
                'data' => $contact_info
            ], 200);
        } catch (\Exception $e) {   
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contact information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateContact(Request $request) 
    {
        $validate = $request->validate([
            'contactID' => 'required|exists:contact_info,contactID',
            'address' => 'required|string|max:225',
            'gmap' => 'required|string|max:225',
            'tel1' => 'required|string|max:225',
            'tel2' => 'required|string|max:225',
            'email' => 'required|string|max:225',
            'twt' => 'required|string|max:225',
            'fb' => 'required|string|max:225',
            'ig' => 'required|string|max:225',
            'iframe' => 'required|string|max:225'
        ]);

        try {
            $contact_info = ContactInfo::findOrFail($validate['contactID']);
            $contact_info->update($validate);

            return response()->json([
                'success' => true,
                'message' => 'Contact information updated successfully.',
                'data' => $contact_info
            ], 200);
        }  catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexTeams()
    {
        try {
            $team_info = TeamInfo::all();

            return response()->json([
                'success' => true,
                'data' => $team_info
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeTeam(Request $request)
    {
        $validate = $request->validate([
            'member_name' => 'required|string|max:255',
            'member_role' => 'required|string|max:255',
            'member_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);
    
        try {
            $random_number = mt_rand(1000000, 9999999);

            $image_name = 'IMG_' . $random_number . '.' . $request->file('member_img')->getClientOriginalExtension();
    
            $image_path = $request->file('member_img')->storeAs('images/team', $image_name, 'public');

            $team_info = TeamInfo::create([
                'member_name' => $validate['member_name'],
                'member_role' => $validate['member_role'],
                'member_img' => $image_path,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Team member created successfully.',
                'data' => $team_info
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create team member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyTeam($id)
    {
        try {
            $team_info = TeamInfo::findOrFail($id);

            if ($team_info->member_img) {
                Storage::disk('public')->delete($team_info->member_img);
            }

            $team_info->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team member removed successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove team member: ' . $e->getMessage()
            ], 500);
        }
    }
}
