<?php

namespace App\Handlers;

class ImageUploadHandler
{
    protected $allow_ext = ['png', 'jpg', 'gif', 'jpeg'];

    // 保存图片核心工具类
    public function save($file, $folder, $file_prefix)
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

        return [
            'path' => config('app.url') . "/$folder_name$filename"
        ];
    }
}
