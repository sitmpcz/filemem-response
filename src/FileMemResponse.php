<?php

declare(strict_types=1);

namespace Sitmpcz;

use Nette;

/**
 * File download response from memory for Nette
 */
class FileMemResponse implements Nette\Application\Response
{
    use Nette\SmartObject;

    /** @var string */
    private string $content;

    /** @var string */
    private string $contentType;

    /** @var string */
    private string $name;

    /** @var ?int */
    private ?int $length;

    /** @var bool */
    private bool $inline;

    function __construct(string &$content, string $name, int $length, ?string $contentType = NULL, bool $inline = false)
    {
        $this->content = $content;
        // pozor - webalize nahradi tecku za priponou pomlckou - to je spatne - posledni tecka tam musi zustat
        //$this->name = \Nette\Utils\Strings::webalize($name);
        // toAscii tam zase nechava mezery, a ty predchozi tecky, ale to by asi nemela byt tragedie
        $this->name = Nette\Utils\Strings::toAscii($name);
        $this->length = $length;
        $this->contentType = $contentType ? $contentType : 'application/octet-stream';
        $this->inline = $inline;
    }

    /**
     * Sends response to output.
     * @param Nette\Http\IRequest $httpRequest
     * @param Nette\Http\IResponse $httpResponse
     */
    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void
    {
        $httpResponse->setContentType($this->contentType);
        if (!$this->inline) {
            $httpResponse->setHeader('Content-Disposition', 'attachment; filename="' . $this->name . '"');
        } else {
            $httpResponse->setHeader('Content-Disposition', 'inline; filename="' . $this->name . '"');
        }
        if ($this->length) {
            $httpResponse->setHeader('Content-Length', strval($this->length));
        }
        echo $this->content;
    }

}
