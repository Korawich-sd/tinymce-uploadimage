<!DOCTYPE html>

<?php
require_once('config/tiny_db.php');

$row = $conn->prepare("SELECT * FROM tiny");
$row->execute();
$q = $row->fetchAll();

if (isset($_POST['save'])) {
	$content = $_POST['message'];

	$stmt = $conn->prepare("INSERT INTO tiny(content) VALUES(:content)");
	$stmt->bindParam(":content", $content);
	$stmt->execute();


	if ($stmt) {
		echo '<script>alert("Success");</script>';
	} else {
		echo '<script>alert("Fail");</script>';
	}
}


?>



<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="tinymce/js/tinymce/tinymce.min.js"></script>
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

	<script>
		const image_upload_handler_callback = (blobInfo, progress) => new Promise((resolve, reject) => {
			const xhr = new XMLHttpRequest();
			xhr.withCredentials = false;
			xhr.open('POST', 'upload.php');

			xhr.upload.onprogress = (e) => {
				progress(e.loaded / e.total * 100);
			};

			xhr.onload = () => {
				if (xhr.status === 403) {
					reject({
						message: 'HTTP Error: ' + xhr.status,
						remove: true
					});
					return;
				}

				if (xhr.status < 200 || xhr.status >= 300) {
					reject('HTTP Error: ' + xhr.status);
					return;
				}

				const json = JSON.parse(xhr.responseText);

				if (!json || typeof json.location != 'string') {
					reject('Invalid JSON: ' + xhr.responseText);
					return;
				}

				resolve(json.location);
			};

			xhr.onerror = () => {
				reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
			};

			const formData = new FormData();
			formData.append('file', blobInfo.blob(), blobInfo.filename());

			xhr.send(formData);
		});

		tinymce.init({
			selector: 'textarea',
			height: 500,
			plugins: 'autolink  code  image  lists table   wordcount',
			toolbar: ' blocks fontfamily fontsize code | bold italic underline strikethrough |  image table  mergetags | addcomment showcomments  | align lineheight | checklist numlist bullist indent outdent | removeformat',
			images_upload_url: 'upload.php',
			images_upload_handler: image_upload_handler_callback
		});
	</script>
</head>

<body>

	<div class="container">
		<div class="row">
			<h2>TinyMCE Upload Image with Ajax and PHP</h2>
			<form  method="post" enctype="multipart/form-data">

				<textarea  name="message"><?php echo $q[0]['content']; ?></textarea>
				<button type="submit" name="save">Save</button>

			</form>
		</div>

		<?php 
		for($i = 0;$i<count($q);$i++){
			echo $q[$i]['content'];
		}
		?>
	</div>

</body>

</html>