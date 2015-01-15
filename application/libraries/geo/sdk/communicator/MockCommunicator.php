<?php

class MockCommunicator implements GoogleServiceCommunicator
{
  protected $urls = array();

  public function requestUrl( $url )
  {
    $response = $this->urls[$url];
    if ( false === $response )
    {
      throw new GoogleGeocodeCommunicatorException( 'Communicator failed' );
    }
    return $response;
  }

  public function seed( $url, $response )
  {
    $this->urls[$url] = $response;
  }
}