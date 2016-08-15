<html>
<head>
<title>Upload Form</title>
</head>
<body>

<?php echo $error;?>

<?php echo form_open_multipart('test/mongo_gridfs/do_upload');?>

<input type="file" name="Filedata" size="20" />

<br /><br />

<input type="submit" value="upload" />

</form>

</body>
</html>
