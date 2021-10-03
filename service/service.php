<?php
namespace VetTix;
use Httpful\Request;
class Service
{

    public static string $checkService = "I am the Service.";
    private static string $baseUrl = "https://www.vettix.org/uapi";

    public static function login($email, $password)
    {
        $loginUrl = self::$baseUrl . "/user/login";
        $body = (object) [
            'email' => $email,
            'password' => $password
        ];
        $loginResponse = Request::post($loginUrl)
            ->sendsAndExpects("application/json")
            ->body(json_encode($body))
            ->send();

        if ($loginResponse->code == "200") {
            $token = $loginResponse->body->token;
            $GLOBALS["token"] = $token;
            return $token;
        } else {
            return $loginResponse->body->errorCode . ": " . $loginResponse->body->message;
        }
    }

    public static function getUserInfo($token)
    {
        $url = self::$baseUrl . "/user/info";
        $response = Request::get($url)
            ->sendsAndExpects("application/json")
            ->addHeader("Authorization", "Bearer " . $token)
            ->send();

        return self::handleResponse($response);

    }

    public static function getStatesWithEvents($token)
    {
        $url = self::$baseUrl . "/state";
        $response = Request::get($url)
            ->sendsAndExpects("application/json")
            ->addHeader("Authorization", "Bearer " . $token)
            ->send();

        return self::handleResponse($response);
    }

    public static function getEventTypes($token)
    {
        $url = self::$baseUrl . "/event/type";
        $response = Request::get($url)
            ->sendsAndExpects("application/json")
            ->addHeader("Authorization", "Bearer " . $token)
            ->send();

        return self::handleResponse($response);
    }

    public static function getEvents($token, $eventTypeID, $stateCode, $eventStatus, $sortBy, $count, $start)
    {
        $url = self::$baseUrl . "/event"
            . "?eventTypeID=" . $eventTypeID
            . "&stateCode=" . $stateCode
            . "&eventStatus=" . $eventStatus
            . "&sortBy=" . $sortBy
            . "&count=" . $count
            . "&start=" . $start;
        $response = Request::get($url)
            ->sendsAndExpects("application/json")
            ->addHeader("Authorization", "Bearer " . $token)
            ->send();

        return self::handleResponse($response);
    }

    public static function getEventDetails($token, $eventId)
    {
        $url = self::$baseUrl . "/event/" . $eventId;
        $response = Request::get($url)
            ->sendsAndExpects("application/json")
            ->addHeader("Authorization", "Bearer " . $token)
            ->send();

        return self::handleResponse($response);
    }

    public static function getInventory($token, $eventId)
    {
        $url = self::$baseUrl . "/inventory/" . $eventId;
        $response = Request::get($url)
            ->sendsAndExpects("application/json")
            ->addHeader("Authorization", "Bearer " . $token)
            ->send();

        return self::handleResponse($response);
    }

    public static function getAdjacentSeats($token, $eventId, $numberOfSeats)
    {

        function cmp($a, $b): int
        {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }

        //todo: finish this with code to find seats
        $url = self::$baseUrl . "/inventory/" . $eventId;
        $response = Request::get($url)
            ->sendsAndExpects("application/json")
            ->addHeader("Authorization", "Bearer " . $token)
            ->send();


        $seatInventory = self::handleResponse($response);

        if (is_array($seatInventory)) {
            $rowsOfSeats = [];
            foreach ($seatInventory as $seat) {
                $rowsOfSeats['section: ' . $seat->section . ' row: ' . $seat->row][] = $seat;
            }
            foreach ($rowsOfSeats as $arrayOfSeats) {


                //check to see if there are enough seats in this row
                if(count($arrayOfSeats) >= $numberOfSeats) {
                    //sort seats in ascending order
                    usort($arrayOfSeats, 'self::cmp');

                    $adjacentSeats = [];
                    //always put the first item as the first adjacent seat
                    array_push($adjacentSeats, $arrayOfSeats[0]);

                    for($i = 1; $i < count($arrayOfSeats) - 1; $i++)
                    {
                        //if it's the next consecutive seat number
                        $currentSeatNumber = $arrayOfSeats[$i] -> seat;
                        $lastSeatNumberInArray = $adjacentSeats[count($adjacentSeats) -1] -> seat;
                        if( $currentSeatNumber == $lastSeatNumberInArray + 1){

                            //push it to the array of seat
                            array_push($adjacentSeats, $arrayOfSeats[$i]);
                            if(count($adjacentSeats) >= $numberOfSeats)
                            {
                                return  $adjacentSeats;
                            }

                        }
                        else{
                            //otherwise test next set of seats to see if there is a big enough group
                            $adjacentSeats = [];
                            array_push($adjacentSeats, $arrayOfSeats[$i]);
                        }
                    }
                }

            }

            return $seatInventory;
        } else
            return $response;
    }

    private static function handleResponse($response)
    {
        if ($response->code == "200") {
            return $response->body;
        } else {
            return $response->body->errorCode . ": " . $response->body->message;
        }
    }

    private static function cmp($a, $b): int
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
}