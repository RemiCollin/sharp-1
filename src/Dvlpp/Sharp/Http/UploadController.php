<?php

namespace Dvlpp\Sharp\Http;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class UploadController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $tab = $this->uploadFile($request);

            return response()->json(["file" => $tab]);

        } catch (\Exception $e) {
            return response()->json(["err" => $e->getMessage()]);
        }
    }

    /**
     * @param $fileShortPath
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($fileShortPath)
    {
        if(strstr($fileShortPath, ":")) {
            list($disk, $path) = explode(":", $fileShortPath);
        } else {
            $disk = config("sharp.upload_storage_disk");
            $path = $fileShortPath;
        }

        return response()->download(
            get_file_path($path, $disk),
            basename($path)
        );
    }

    private function uploadFile(Request $request)
    {
        $file = $request->file('file');

        if ($file) {
            $filename = uniqid() . "." . $file->getClientOriginalExtension();
            $filesize = $file->getSize();

            $file->move($this->getTmpUploadDirectory(), $filename);

            return [
                "name" => $filename,
                "size" => $filesize,
                "path" => $this->getTmpUploadDirectory() . "/" . $filename
            ];
        }

        throw new FileNotFoundException;
    }

    private function getTmpUploadDirectory()
    {
        $dir = get_file_path(config("sharp.upload_tmp_base_path"));

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

} 