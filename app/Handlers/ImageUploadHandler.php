<?php

namespace App\Handlers;

use Image; //对应Intervention\Image\Facades\Image类

class ImageUploadHandler
{
    protected $allow_ext = ['png', 'jpg', 'gif', 'jpeg'];

    // 保存图片核心工具类
    public function save($file, $folder, $file_prefix, $max_width = false)
    {
        // 定义目录路径 (如：uploads/images/avatars/201803/06/)
        $folder_name = "uploads/images/{$folder}/" . date('Ym') . '/' . date('d') . '/';

        //通过public_path()方法获取真实目录路径
        //值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201803/06/
        $upload_path = public_path() . '/' . $folder_name;

        // 获取文件后缀名(因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在)
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'png';

        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $ext;

        if (! in_array($ext, $this->allow_ext)) {
            return false;
        }

        // 将图片移动到我们定义的目标目录($upload_path)中
        $file->move($upload_path, $filename);

        //对图片进行裁剪操作
        if ($max_width && $ext != 'gif') {
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name$filename"
        ];
    }


    /**
     * 对文件尺寸进行裁剪处理
     * @date   2018-03-07
     * @param  [type]     $file_path [description]
     * @param  [type]     $max_width [description]
     * @return [type]                [description]
     */
    public function reduceSize($file_path, $max_width)
    {
        $image = Image::make($file_path);

        // 进行大小调整操作
        $image->resize($max_width, null, function ($constraint) {
            //设定宽度为$max_width,且进行等比例缩放
            $constraint->aspectRatio();

            // 防止截图时图片尺寸变大（这个没看懂什么意思）
            $constraint->upsize();
        });

        $image->save();
    }
}
