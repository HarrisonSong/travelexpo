<html><head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# collegekhabar_etech: http://ogp.me/ns/fb/collegekhabar_etech#">
<meta property="fb:app_id" content="404341769625504"/>
<meta property="og:type" content="travel_expo:friendplace"/>
<?php if(isset($info)) {?>
	<meta property="og:title" content="<?php echo $info['name'];?>"/>
	<meta property="og:url"	content="<?php echo site_url("general/OpenGraph_FriendPlace/".$friend_id."/".$place_id); ?>"/>
	<meta property="og:description" content="<?php echo $info['description'];?>" />
	<meta property="og:image" content="<?php echo $src?>" />
	<meta property="og:location:latitude" content="<?php echo $info['latitude'];?>" />
	<meta property="og:location:longitude" content="<?php echo $info['longitude'];?>" /> 
<?php } else { ?>
	<meta property="og:url" content="<?php echo site_url("general/index"); ?>" />
	<meta property="og:title" content="title" />
	<meta property="og:description" content="description" />
	<meta property="og:image" content="images/logo.png" />
<?php } ?>
</head>
<body>
<script language="javascript">
	location.href = "<?php echo $redirect_link;?>";
</script>
</body>
</html>