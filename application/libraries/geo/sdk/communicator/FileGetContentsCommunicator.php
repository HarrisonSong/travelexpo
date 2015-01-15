<?php
/**
 * Copyright (c) 2010 J. Walter Thompson dba JWT
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

/**
 *
 * @author Peter Bailey <peter.bailey@jwt.com>
 *
 * @package GoogleGeocoding
 * @subpackage Communicator
 *
 * @see http://www.php.net/file_get_contents
 *
 */
class FileGetContentsCommunicator implements GoogleServiceCommunicator
{
  /**
   * Obtain a response from the provded URL
   *
   * @param string $url
   *
   * @return string
   *
   * @throws GoogleGeocodeException
   */
  public function requestUrl( $url )
  {
    $response = file_get_contents( $url, false );
    if ( false === $response )
    {
      throw new GoogleGeocodeException( 'Contacting resource failed' );
    }
    return $response;
  }
}