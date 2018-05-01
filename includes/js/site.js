$(document).ready(function()
{
	if ( typeof( path_var ) != 'undefined' )
	{
		var path = path_var + '/';
	}
	else
	{
		var path = '';
	}

	// Show or hide rent/purchase prices based on type selected
	$('#listing_type').change(function()
	{
		var listing_type = $('#listing_type').val();
		
		// These are hardcoded values -- if you change the values in the admin panel, you must update them here
		if ( listing_type == '2' || listing_type == '4' )
		{
			$('#price_range_rent').show();
			$('#price_range_purchase').hide();
		}
		else
		{
			$('#price_range_purchase').show();
			$('#price_range_rent').hide();
		}
	});

	// Disable the second and third location levels initially
	//$("select#location2").attr("disabled","disabled");
	//$("select#location3").attr("disabled","disabled");
	
	// Monitor the first level location for activity
	$("select#location1").change(function()
	{
		//$("select#location3").attr("disabled","disabled");
		
		var location1 = $("select#location1 option:selected").val();
		
		$.post( path + "includes/ajax.php", { action: 'dropdown', location1: location1 }, function(data)
		{
			if ( data != 'empty' )
			{
				$("select#location2").removeAttr("disabled");
				$("select#location2").html(data);
				$("select#location2").show();
			}
			else
			{
				$("select#location2").hide();
				$("select#location3").hide();
			}
		});
	});

	// Monitor the second level location for activity
	$("select#location2").change(function()
	{
		var location2 = $("select#location2 option:selected").val();
	
		$.post( path + "includes/ajax.php", { action: 'dropdown', location2: location2 }, function(data)
		{
			if ( data != 'empty' )
			{
				$("select#location3").removeAttr("disabled");
				$("select#location3").html(data);
				$("select#location3").show();
			}
			else
			{
				$("select#location3").hide();
			}
		});
	});
	
	var settings = {
		url: path + "includes/ajax.php?action=bulk_upload",
		method: "POST",
		allowedTypes: "jpg,jpeg,png,gif",
		fileName: "myfile",
		multiple: true,
		onSuccess:function(files,data,xhr)
		{
			$("#status").html("<font color='green'>The files have been uploaded successfully.</font>");
	    },
	    afterUploadAll:function()
	    {
	    	//alert( "All of the images have been uploaded!" );
	    },
		onError: function(files,status,errMsg)
		{		
			$("#status").html("<font color='red'>The files could not be uploaded. Please try again.</font>");
		}
	}
	
	if ( $( '#fileuploader' ).length )
	{
		$( '#fileuploader' ).uploadFile(settings);
	}
});

function textCounter( field, countfield, maxlimit ) 
{
	if (field.value.length > maxlimit)
		field.value = field.value.substring(0, maxlimit);
	else 
		countfield.value = maxlimit - field.value.length;
}