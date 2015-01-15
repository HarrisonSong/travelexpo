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
 * A response class for Google's Geocoding API (V3)
 *
 * @author Peter Bailey <peter.bailey@jwt.com>
 *
 * @package GoogleGeocoding
 * @subpackage Core
 *
 * @see http://code.google.com/apis/maps/documentation/geocoding/
 */
class GoogleGeocodeResponseV3 implements Iterator, Countable
{
  // Statuses
  const STATUS_OK               = "OK";
  const STATUS_ZERO_RESULTS     = "ZERO_RESULTS";
  const STATUS_OVER_QUERY_LIMIT = "OVER_QUERY_LIMIT";
  const STATUS_REQUEST_DENIED   = "REQUEST_DENIED";
  const STATUS_INVALID_REQUEST  = "INVALID_REQUEST";

  // Address Component Types
  const ACT_STREET_ADDRESS              = 'street_address';
  const ACT_ROUTE                       = 'route';
  const ACT_INTERSECTION                = 'intersection';
  const ACT_POLITICAL                   = 'political';
  const ACT_COUNTRY                     = 'country';
  const ACT_ADMINISTRATIVE_AREA_LEVEL_1 = 'administrative_area_level_1';
  const ACT_ADMINISTRATIVE_AREA_LEVEL_2 = 'administrative_area_level_2';
  const ACT_ADMINISTRATIVE_AREA_LEVEL_3 = 'administrative_area_level_3';
  const ACT_COLLOQUIAL_AREA             = 'colloquial_area';
  const ACT_LOCALITY                    = 'locality';
  const ACT_SUBLOCALITY                 = 'sublocality';
  const ACT_NEIGHBORHOOD                = 'neighborhood';
  const ACT_PREMISE                     = 'premise';
  const ACT_SUBPREMISE                  = 'subpremise';
  const ACT_POSTAL_CODE                 = 'postal_code';
  const ACT_NATURAL_FEATURE             = 'natural_feature';
  const ACT_AIRPORT                     = 'airport';
  const ACT_PARK                        = 'park';
  const ACT_POINT_OF_INTEREST           = 'point_of_interest';
  const ACT_POST_BOX                    = 'post_box';
  const ACT_STREET_NUMBER               = 'street_number';
  const ACT_FLOOR                       = 'floor';
  const ACT_ROOM                        = 'room';

  // Location Types
  const LT_ROOFTOP            = 'ROOFTOP';
  const LT_RANGE_INTERPOLATED = 'RANGE_INTERPOLATED';
  const LT_GEOMETRIC_CENTER   = 'GEOMETRIC_CENTER';
  const LT_APPROXIMATE        = 'APPROXIMATE';

  const NAME_SHORT = "short_name";
  const NAME_LONG  = "long_name";

  /**
   * The parsed response
   *
   * @var stdClass
   */
  protected $response;

  /**
   * The un-parsed response
   *
   * @var string
   */
  protected $rawResponse;

  /**
   * Statuses which are considered valid
   *
   * @var array
   */
  protected static $validStatuses = array(
      self::STATUS_OK
    , self::STATUS_ZERO_RESULTS
  );

  /**
   * Statuses which are considered invalid
   *
   * @var array
   */
  protected static $invalidStatuses = array(
      self::STATUS_OVER_QUERY_LIMIT
    , self::STATUS_REQUEST_DENIED
    , self::STATUS_INVALID_REQUEST
  );

  /**
   * Cursor for managing iteration or results
   *
   * @var integer
   */
  private $cursor = 0;

  /**
   * Constructor
   *
   * @param string $response Raw response from API
   * @param string $responseFormat Format that response is in
   *
   * @throws GoogleGeocodeException
   */
  public function __construct( $response, $responseFormat )
  {
    $this->rawResponse = $response;
    switch ( $responseFormat )
    {
      case GoogleGeocodeServiceV3::FORMAT_JSON:
        $response = json_decode( $response );
        break;
      case GoogleGeocodeServiceV3::FORMAT_XML:
        throw new GoogleGeocodeException( 'XML parsing is not yet supported' );
        break;
    }
    $this->response = $response;
  }

  /**
   * Obtain the requested address component, if it exists
   *
   * @param string $componentType
   *
   * @return stdClass|null
   */
  protected function getAddressComponent( $componentType )
  {
    foreach ( $this->current()->address_components as $component )
    {
      if ( in_array( $componentType, $component->types ) )
      {
        return $component;
      }
    }
    return null;
  }

  /**
   * Check the response for results
   *
   * @return boolean
   */
  public function hasResults()
  {
    return ( self::STATUS_ZERO_RESULTS != $this->getStatus() );
  }

  /**
   * Check the response for validity
   *
   * @return boolean
   */
  public function isValid()
  {
    return ( in_array( $this->getStatus(), self::$validStatuses ) );
  }

  /**
   * Check the response for invalidity
   *
   * @return boolean
   */
  public function isInvalid()
  {
    return ( in_array( $this->getStatus(), self::$invalidStatuses ) );
  }

  /**
   * Obatin the types for the result at the current cursor
   *
   * @return array
   */
  public function getTypes()
  {
    return $this->current()->types;
  }

  /**
   * Obtain the name of an address component for the result at the
   * current cursor
   *
   * @param string $component Which address component to read
   * @param string $size Which name length to return
   *
   * @return string|null
   */
  public function getAddressComponentName( $component, $size = self::NAME_LONG )
  {
    $component = $this->getAddressComponent( $component );
    if ( $component )
    {
      return $component->$size;
    }
    return null;
  }

  /**
   * Determine if the result at the current cursor is of the given type
   *
   * @param string $type
   *
   * @return boolean
   */
  public function assertType( $type )
  {
    return ( $this->hasResults() )
      ? in_array( $type, $this->getTypes() )
      : false
    ;
  }

  /**
   * Obtain the parsed response
   *
   * @return stdClass
   */
  public function getResponse()
  {
    return $this->response;
  }

  /**
   * Obtain the response pre-parsing
   *
   * @return string
   */
  public function getRawResponse()
  {
    return $this->rawResponse;
  }

  /**
   * Obtain the response's status
   *
   * @return string
   */
  public function getStatus()
  {
    return $this->response->status;
  }

  /**
   * Obtain the formatted address of the result at the current cursor
   *
   * @return string
   */
  public function getFormattedAddress()
  {
    return $this->current()->formatted_address;
  }

  /**
   * Obtain the location object from the result at the current cursor
   *
   * @return stdClass {"lat": FLOAT, "lng": FLOAT}
   */
  public function getLocation()
  {
    return $this->current()->geometry->location;
  }

  /**
   * Obtain the viewport object from the result at the current cursor
   *
   * @return stdClass { "southwest": {"lat": FLOAT, "lng": FLOAT}
   *                  , "northeast": {"lat": FLOAT, "lng": FLOAT}}
   */
  public function getViewport()
  {
    return $this->current()->geometry->viewport;
  }

  /**
   * Obtain the bounds object from the result at the current cursor
   *
   * @return stdClass { "southwest": {"lat": FLOAT, "lng": FLOAT}
   *                  , "northeast": {"lat": FLOAT, "lng": FLOAT}}
   */
  public function getBounds()
  {
    return $this->hasBounds()
      ? $this->current()->geometry->bounds
      : null
    ;
  }

  /**
   * Determine if the result at the current cursor includes the optional
   * bounds property
   *
   * @return boolean
   */
  public function hasBounds()
  {
    return isset( $this->current()->geometry->bounds );
  }

  /**
   * Obtain the location type of the result at the current cursor
   *
   * @return string
   */
  public function getLocationType()
  {
    return $this->current()->geometry->location_type;
  }

  /**
   * Reset the cursor
   *
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function rewind()
  {
    $this->cursor = 0;
  }

  /**
   * Obtain the result at the current cursor
   *
   * @return stdClass|null
   *
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function current()
  {
    return isset( $this->response->results[$this->cursor] )
      ? $this->response->results[$this->cursor]
      : null
    ;
  }

  /**
   * Retrieve the cursor
   *
   * @return integer
   *
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function key()
  {
    return $this->cursor;
  }

  /**
   * Advance the cursor
   *
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function next()
  {
    $this->cursor += 1;
  }

  /**
   * Determine if the cursor is valid
   *
   * @return boolean
   *
   * @see http://www.php.net/manual/en/class.iterator.php
   */
  public function valid()
  {
    if ( isset( $this->response->results[$this->cursor] ) )
    {
      return true;
    }
    $this->rewind();
    return false;
  }

  /**
   * Count the number of results in the response
   *
   * @return integer
   *
   * @see http://www.php.net/manual/en/class.countable.php
   */
  public function count()
  {
    return count( $this->response->results );
  }
}