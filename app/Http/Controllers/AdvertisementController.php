<?php

namespace App\Http\Controllers;

use App\Models\AdImages;
use Illuminate\Support\Facades\Input;

class AdvertisementController extends Controller
{

    /**
     * @api {get} advertisement/{business_id} Fetch Business Image Ads
     * @apiName FetchAdvertisementImage
     * @apiGroup Broadcast
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/advertisement/1
     * @apiDescription Gets all the image advertisements that have been uploaded by the business.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission none
     *
     * @apiParam {Number} business_id The id of the business.
     *
     * @apiSuccess (200) {Object[]} ad_images The array of images found on the broadcast screen.
     * @apiSuccess (200) {Number} ad_images.img_id The id of the image.
     * @apiSuccess (200) {String} ad_images.path The filesystem path of the image.
     * @apiSuccess (200) {Number} ad_images.weight The weight/place of the image.
     * @apiSuccess (200) {Number} ad_images.business_id The id of the business to which the image belongs.
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "img_id": 72,
     *         "path": "ads\/125\/o_1a2pute0r17ns1fi91p8q1vj6ric.jpg",
     *         "weight": 19,
     *         "business_id": 125
     *       },
     *       {
     *         "img_id": 74,
     *         "path": "ads\/125\/o_1a2pute0rmt3nm7f5o10927tue.png",
     *         "weight": 21,
     *         "business_id": 125
     *       }
     *     ]
     *
     * @apiError (Error) {String} NoImagesFound No images were found using the <code>business_id</code>.
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "err_code": "NoImagesFound"
     *     }
     */
    public function getImages($business_id = 0)
    {
        if (AdImages::imageExistsByBusinessId($business_id)) {
            $ad_images = AdImages::fetchAllImagesByBusinessId($business_id);
            return $ad_images;
        } else {
            return json_encode(array(
                'err_code' => 'NoImagesFound'
            ));
        }
    }

    /**
     * @api {post} /ads/upload/{business_id} Upload business image.
     * @apiName UploadBusinessImage
     * @apiGroup Advertisement
     * @apiVersion 1.0.0
     * @apiExample {js} Example Usage:
     *     https://api.featherq.com/ads/upload/1
     * @apiDescription Upload image to business.
     *
     * @apiHeader {String} access-key The unique access key sent by the client.
     * @apiPermission Authenticated Business Admin
     *
     * @apiParam {Number} business_id Unique ID of business to add this feature to.
     * @apiParam {ImageFile} image Image file as part of form data to be uploaded.
     *
     * @apiSuccess (200) {Number} success Process success flag.
     *
     * @apiSuccessExample {Json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "success": 1
     *      }
     *
     * @apiError (Error) {Number} success Process fail flag.
     * @apiError (Error) {String} err_code UnauthorizedUser User does not have admin rights.
     * @apiError (Error) {String} err_code BusinessNotFound No business matched using <code>business_id</code>
     * @apiError (Error) {String} err_code UploadFailed Internal error during upload. Please try again.
     *
     * @apiErrorExample {Json} Error-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": 0,
     *       "err_code": "UnauthorizedUser"
     *     }
     */
    public function postUploadImage($business_id = null)
    {
        // TODO authentication
        if (true) {
            $business = Business::getBusinessByBusinessId($business_id);
            if (is_null($business)) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'BusinessNotFound'
                ));
            }

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", FALSE);
            header("Pragma: no-cache");

            @set_time_limit(5 * 60);

            $targetDir = base_path('public/ads/' . $business_id);
            $cleanupTargetDir = TRUE; // Remove old files
            //$maxFileAge = 5 * 3600; // Temp file age in seconds

            if (!file_exists($targetDir)) {
                @mkdir($targetDir);
            }

            if (isset($_REQUEST["name"])) {
                $fileName = $_REQUEST["name"];
            } elseif (!empty($_FILES)) {
                $fileName = $_FILES["file"]["name"];
            } else {
                $fileName = uniqid("file_");
            }

            $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
            $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
            $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

            // Clean the fileName for security reasons
            $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

            // Make sure the fileName is unique but only if chunking is disabled
            if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
                $ext = strrpos($fileName, '.');
                $fileName_a = substr($fileName, 0, $ext);
                $fileName_b = substr($fileName, $ext);

                $count = 1;
                while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) {
                    $count++;
                }

                $fileName = $fileName_a . '_' . $count . $fileName_b;
            }

            $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

            // Remove old temp files
            if ($cleanupTargetDir) {
                if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                    return json_encode(array(
                        'success' => 0,
                        'err_code' => 'UploadFailed'
                    ));
                }

                while (($file = readdir($dir)) !== FALSE) {
                    $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                    // If temp file is current file proceed to the next
                    if ($tmpfilePath == "{$filePath}.part") {
                        continue;
                    }

                    // Remove temp file if it is older than the max age and is not the current file
                    //if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    //  @unlink($tmpfilePath);
                    //}
                }
                closedir($dir);
            }


            // Open temp file
            if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
                return json_encode(array(
                    'success' => 0,
                    'err_code' => 'UploadFailed'
                ));
            }

            if (!empty($_FILES)) {
                if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                    return json_encode(array(
                        'success' => 0,
                        'err_code' => 'UploadFailed'
                    ));
                }

                // Read binary input stream and append it to temp file
                if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                    return json_encode(array(
                        'success' => 0,
                        'err_code' => 'UploadFailed'
                    ));
                }
            } else {
                if (!$in = @fopen("php://input", "rb")) {
                    return json_encode(array(
                        'success' => 0,
                        'err_code' => 'UploadFailed'
                    ));
                }
            }

            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }

            @fclose($out);
            @fclose($in);

            // Check if file has been uploaded
            if (!$chunks || $chunk == $chunks - 1) {
                // Strip the temp .part suffix off
                rename("{$filePath}.part", $filePath);
            }


            AdImages::saveImages('ads/' . $business_id . '/' . basename($filePath), $business_id);

            return json_encode(array(
                'success' => 1
            ));
        } else {
            return json_encode(array(
                'success' => 0,
                'err_code' => 'UnauthorizedUser'
            ));
        }
    }
}