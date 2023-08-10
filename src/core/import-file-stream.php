<?php
/**
 * I chose to use the JsonStreamingParser library to parse the JSON file.
 * This library is very fast and memory efficient and it can parse JSON files of huge size.
 */
 
use JsonStreamingParser\Listener;
 
class CustomListener implements \JsonStreamingParser\Listener\ListenerInterface {
    private $currentKey = null;
    private $currentObject = [];
    private $deviceIds = [];
    private $depth = 0;
    private $pdo;
    private $userManager;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->userManager = new UserManager($pdo);
    }

    public function startDocument(): void
    {
        // TODO: Implement startDocument() method.
    }


    public function endDocument() : void {
        // TODO: Implement endDocument() method.
    }

    public function startObject() : void {
        $this->depth++;
    }

    public function endObject() : void {
        $this->depth--; 
        if ($this->depth === 1 && $this->currentKey === 'devices') {
            $this->currentObject['deviceId'] = $this->deviceIds;
            $this->deviceIds = [];
        } elseif ($this->depth === 0) { 
            // If we are at the end of a user object, we can update the user data
            $this->userManager->updateUserData($this->currentObject, $this->deviceIds);
      
            $this->currentObject = [];
            $this->deviceIds = [];

        }
    }

    public function startArray() : void {
        // TODO: Implement startArray() method.
    }

    public function endArray() : void {
        // TODO: Implement endArray() method.
    }

    public function key(string $key): void {
         $this->currentKey = $key;
    }

    public function value($value) { 
        if ($this->currentKey === 'deviceId') {
            $this->deviceIds[] = $value;
        } else {
            $this->currentObject[$this->currentKey] = $value;
        }
    }
    public function whitespace($whitespace): void {
        // TODO: Implement whitespace() method.
    }

}
// Using stream to read large json file
function ImportJsonDataToDB($pdo, $jsonFile) {
    try{
        $stream = fopen($jsonFile, 'r');
        $userManager = new UserManager($pdo);
        $listener = new CustomListener($pdo);
        $parser = new \JsonStreamingParser\Parser($stream, $listener);
        $parser->parse();
        fclose($stream);
        return true;
    }catch(Exception $e){
        echo $e->getMessage();
    }

}

 