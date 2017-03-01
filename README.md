# mobileeditor
移动简易编辑器arteditor的应用带服务端php
使用了下面的改进，过滤了换行div为p

# artEditor   
artEditor是一款基于jQuery的移动端富文本编辑器，支持插入图片，后续完善其他功能。   
[demo](http://baixuexiyang.github.io/artEditor/)，为了更好的效果请将浏览器设置为手机模式        
# 引用
在页面中引入下面资源   
```
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>     
<script src="artEditor.min.js"></script>       
```   
    
# Options  
### imgTar  
  图片上传按钮     
### limitSize   
  图片最大限制，默认3兆   
### showServer    
  显示从服务端返回的图片，默认是显示本地资源的图片       
### uploadUrl    
  图片上传路劲       
### data    
  上传图片其他参数       
### uploadField    
  上传图片字段       
### placeholader    
  富文本编辑器holder       
### validHtml    
  粘贴时，去除不合法的html标签       
### uploadSuccess    
  图片上传成功回调       
### uploadError    
  图片上传失败回调       
### formInputId     
  表单隐藏域id，如果设置，则编辑器内容会储存到该元素value值         
    

# Methods      
  
### getValue   
    获取值，$('#content').getValue()    
### setValue   
    设置值，$('#content').setValue('<div></div>')    
    
     
# Example
html:
```
<div class="article-content" id="content">
</div>
```
js:

```
$('#content').artEditor({
	imgTar: '#imageUpload',
	limitSize: 5,   // 兆
	showServer: false,
	uploadUrl: '',
	data: {},
	uploadField: 'image',
	placeholader: '<p>请输入文章正文内容</p>',
	validHtml: ["br"],
	uploadSuccess: function(res) {
            // 这里是处理返回数据业务逻辑的地方
            // `res`为服务器返回`status==200`的`response`
            // 如果这里`return <path>`将会以`<img src='path'>`的形式插入到页面
            // 如果发现`res`不符合业务逻辑
            // 比如后台告诉你这张图片不对劲
            // 麻烦返回 `false`
            // 当然如果`showServer==false`
            // 无所谓咯
		return res.path;
	},
	uploadError: function(res) {
		//这里做上传失败的操作
        //也就是http返回码非200的时候
		console.log(res);
	}
});
```
# artEditor 之php上传
采用php的base64为上传，
以下我编写的   
```
//base64位图片上传
function base64_upload($base64_img, $config = array())
{
    $default_up_dir = dirname(dirname(__FILE__));
    $default_up_dir = str_replace("\\", "/", $default_up_dir)."/upload/";
    if (!file_exists($default_up_dir)) {
        mkdir($default_up_dir, 0775);
    }
    //$default_file_url = "http://" . $_SERVER['HTTP_HOST'] . "/upload/";.
    $default_file_url="upload/";
    $default_file_type = array('pjpeg', 'jpeg', 'jpg', 'gif', 'bmp', 'png');

    $default = array(
        'dir' => $default_up_dir,
        'picurl'=> $default_file_url,
        'filetype' => $default_file_type
    );
    $default = $config + $default;
    $base64_img = trim($base64_img);
    $data=array();
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {

        $type = $result[2];
        $nfilename = date('YmdHis_') . mt_rand(1, 999) . '.' . $type;
        $picurl=$default['picurl'].$nfilename;
        $tmpfile = base64_decode(str_replace($result[1], '', $base64_img));
        if (file_put_contents($default['dir'] . $nfilename, $tmpfile)) {

            $data = array(
                'code' => 100,
                'summary' => "上传成功",
                'data' => array('url' =>$picurl)
            );

        }else{
            $data = array(
                'code' =>101 ,
                'summary' => "上传失败",
            );

        }

    }else
    {
        $data = array(
            'code' => 102,
            'summary' => "上传文件类型不支持",

        );
    }
    return $data;

}
```