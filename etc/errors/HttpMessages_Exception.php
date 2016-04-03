<?php

namespace Craft;

class HttpMessages_Exception extends \CException
{
    /**
     * Status Code
     *
     * @var int
     */
    protected $status_code = 500;

    /**
     * Status Phrase
     *
     * @var string
     */
    protected $status_phrase = 'Internal Server Error';

    /**
     * Errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Input
     *
     * @var array
     */
    protected $input = [];

    /**
     * Set Status Code
     *
     * @param int $status_code Status Code
     */
    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;

        return $this;
    }

    /**
     * Get Status Code
     *
     * @return int Status Code
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * Set Status Phrase
     *
     * @param string $status_phrase Status Prase
     */
    public function setStatusPhrase($status_phrase)
    {
        $this->status_phrase = $status_phrase;

        return $this;
    }

    /**
     * Get Status Phrase
     *
     * @return string Status Phrase
     */
    public function getStatusPhrase()
    {
        return $this->status_phrase;
    }

    /**
     * Set Status
     *
     * @param int    $status_code   Status Code
     * @param string $status_phrase Status Phrase
     */
    public function setStatus($status_code, $status_phrase = '')
    {
        $this->status_code = $status_code;
        $this->status_phrase = $status_phrase;

        return $this;
    }

    /**
     * Set Message
     *
     * @param string $message Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set Errors
     *
     * @param array $errors Errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get Errors
     *
     * @return array Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Has Errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return empty($this->errors) ? false : true;
    }

    /**
     * Set Input
     *
     * @param array $input Input
     */
    public function setInput(array $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get Input
     *
     * @return array Input
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Has Input
     *
     * @return boolean
     */
    public function hasInput()
    {
        return empty($this->input) ? false : true;
    }
}
