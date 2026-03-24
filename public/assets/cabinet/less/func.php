<?php
function autoCompileLess($inputFile, $outputFile) {
  // load the cache
  $cacheFile = $inputFile.".cache";

  if (file_exists($cacheFile)) {
	$cache = unserialize(file_get_contents($cacheFile));
  } else {
	$cache = $inputFile;
  }

  $less = new lessc;
  $newCache = $less->cachedCompile($cache);

  if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
	file_put_contents($cacheFile, serialize($newCache));
	file_put_contents($outputFile, $newCache['compiled']);
  }
}

function getBaseUrl() {
	// Determine if the request was over SSL (HTTPS).
	if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
	{
		$https = 's://';
	}
	else
	{
		$https = '://';
	}

	/*
	 * Since we are assigning the URI from the server variables, we first need
	 * to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
	 * are present, we will assume we are running on apache.
	 */

	if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
	{
		// To build the entire URI we need to prepend the protocol, and the http host
		// to the URI string.
		$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	else
	{
		/*
		 * Since we do not have REQUEST_URI to work with, we will assume we are
		 * running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
		 * QUERY_STRING environment variables.
		 *
		 * IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
		 */
		$theURI = 'http' . $https . $_SERVER['HTTP_HOST'];
	}

	// Extra cleanup to remove invalid chars in the URL to prevent injections through the Host header
	$theURI = str_replace(array("'", '"', '<', '>'), array("%27", "%22", "%3C", "%3E"), $theURI);

	return $theURI;
}