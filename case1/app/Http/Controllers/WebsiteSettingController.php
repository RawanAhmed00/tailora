<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWebsiteSettingRequest;
use App\Http\Requests\UpdateWebsiteSettingRequest;
use App\Http\Resources\WebsiteSettingResource;
use App\Models\WebsiteSetting;
use App\Traits\UploadImage;
use Illuminate\Support\Facades\DB;

class WebsiteSettingController extends Controller
{
    use UploadImage;

    public function index()
    {
        $settings = WebsiteSetting::with('socialMediaLinks')->paginate(10);

        return WebsiteSettingResource::collection($settings);
    }
    

    public function store(StoreWebsiteSettingRequest $request)
    {
        DB::beginTransaction();

        try {

            $data = $request->validated();

            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadImage(
                    $request->file('logo'),
                    'website/logo'
                );
            }

            if ($request->hasFile('homepage_banner')) {
                $data['homepage_banner'] = $this->uploadImage(
                    $request->file('homepage_banner'),
                    'website/banner'
                );
            }

            $setting = WebsiteSetting::create($data);

            if (!empty($data['social_media_links'])) {

                foreach ($data['social_media_links'] as $social) {

                    $setting->socialMediaLinks()->create($social);

                }
            }

            DB::commit();

            return new WebsiteSettingResource(
                $setting->load('socialMediaLinks')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(WebsiteSetting $websiteSetting)
    {
        return new WebsiteSettingResource(
            $websiteSetting->load('socialMediaLinks')
        );
    }

    public function update(UpdateWebsiteSettingRequest $request, WebsiteSetting $websiteSetting)
    {
        DB::beginTransaction();

        try {

            $data = $request->validated();

            if ($request->hasFile('logo')) {

                $this->deleteImage($websiteSetting->logo);

                $data['logo'] = $this->uploadImage(
                    $request->file('logo'),
                    'website/logo'
                );
            }

            if ($request->hasFile('homepage_banner')) {

                $this->deleteImage($websiteSetting->homepage_banner);

                $data['homepage_banner'] = $this->uploadImage(
                    $request->file('homepage_banner'),
                    'website/banner'
                );
            }

            $websiteSetting->update($data);

            if (isset($data['social_media_links'])) {

                $websiteSetting->socialMediaLinks()->delete();

                foreach ($data['social_media_links'] as $social) {

                    $websiteSetting->socialMediaLinks()->create($social);

                }
            }

            DB::commit();

            return new WebsiteSettingResource(
                $websiteSetting->load('socialMediaLinks')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(WebsiteSetting $websiteSetting)
    {
        $this->deleteImage($websiteSetting->logo);
        $this->deleteImage($websiteSetting->homepage_banner);

        $websiteSetting->socialMediaLinks()->delete();
        $websiteSetting->delete();

        return response()->json([
            'message' => 'Website setting deleted successfully.'
        ]);
    }
}