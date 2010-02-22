<?php
/*
 *  $Id: AbstractSAXParser.php,v 1.13 2004/03/20 03:33:06 hlellelid Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 */

/**
 * The abstract SAX parser class.
 *
 * This class represents a SAX parser. It is a abstract calss that must be
 * implemented by the real parser that must extend this class
 *
 * @author    Andreas Aderhold <andi@binarycloud.com>
 * @author    Hans Lellelid <hans@xmpl.org>
 * @copyright � 2001,2002 THYRELL. All rights reserved
 * @version   $Revision: 1.13 $
 * @package   phing.parser
 */
abstract class AbstractSAXParser {
    
    /** The AbstractHandler object. */
    protected $handler;

    /**
     * Constructs a SAX parser
     */
    function __construct() {}

    /**
     * Sets options for PHP interal parser. Must be implemented by the parser
     * class if it should be used.
     */
    abstract function parserSetOption($opt, $val);

    /**
     * Sets the current element handler object for this parser. Usually this
     * is an object using extending "AbstractHandler".
     *
     * @param AbstractHandler $obj The handler object.
     */
    function setHandler( $obj) {
        $this->handler = $obj;
    }

    /**
     * Method that gets invoked when the parser runs over a XML start element.
     *
     * This method is called by PHP's internal parser funcitons and registered
     * in the actual parser implementation.
     * It gives control to the current active handler object by calling the
     * <code>startElement()</code> method.
     * 
     * BECAUSE OF PROBLEMS WITH EXCEPTIONS BUBBLING UP THROUGH xml_parse() THIS
     * METHOD WILL CALL Phing::halt(-1) ON EXCEPTION.
     *
     * @param  object  the php's internal parser handle
     * @param  string  the open tag name
     * @param  array   the tag's attributes if any
     */
    function startElement($parser, $name, $attribs) {
        try {
            $this->handler->startElement($name, $attribs);        
        } catch (Exception $e) {
            print "[Exception in XML parsing]\n";
            print $e;
            Phing::halt(-1);
        }
    }

    /**
     * Method that gets invoked when the parser runs over a XML close element.
     *
     * This method is called by PHP's internal parser funcitons and registered
     * in the actual parser implementation.
     *
     * It gives control to the current active handler object by calling the
     * <code>endElement()</code> method.
     *
     * BECAUSE OF PROBLEMS WITH EXCEPTIONS BUBBLING UP THROUGH xml_parse() THIS
     * METHOD WILL CALL Phing::halt(-1) ON EXCEPTION.
     * 
     * @param   object  the php's internal parser handle
     * @param   string  the closing tag name
     */
    function endElement($parser, $name) {
        try {
            $this->handler->endElement($name);
        } catch (Exception $e) {
            print "[Exception in XML parsing]\n";
            print $e;
            Phing::halt(-1);
        }
    }

    /**
     * Method that gets invoked when the parser runs over CDATA.
     *
     * This method is called by PHP's internal parser functions and registered
     * in the actual parser implementation.
     *
     * It gives control to the current active handler object by calling the
     * <code>characters()</code> method. That processes the given CDATA.
     *
     * BECAUSE OF PROBLEMS WITH EXCEPTIONS BUBBLING UP THROUGH xml_parse() THIS
     * METHOD WILL CALL Phing::halt(-1) ON EXCEPTION.
     * 
     * @param resource $parser php's internal parser handle.
     * @param string $data the CDATA
     */
    function characters($parser, $data) {
        try {     
            $this->handler->characters($data);        
        } catch (Exception $e) {
            print "[Exception in XML parsing]\n";
            print $e;
            Phing::halt(-1);
        }
    }

    /**
     * Entrypoint for parser. This method needs to be implemented by the
     * child classt that utilizes the concrete parser
     */
    abstract function parse();
}
