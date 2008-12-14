<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/TokenizerI.php';

/**
 * This tokenizer uses the internal {@link token_get_all()} function as token stream
 * generator.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 *
 */
class PHP_Depend_Code_Tokenizer_InternalTokenizer
    implements PHP_Depend_Code_TokenizerI
{
    /**
     * Mapping between php internal tokens and php depend tokens.
     *
     * @var array(integer=>integer) $tokenMap
     */
    protected static $tokenMap = array(
        T_AS                        =>  self::T_AS,
        T_DO                        =>  self::T_DO,
        T_IF                        =>  self::T_IF,
        T_SL                        =>  self::T_SL,
        T_SR                        =>  self::T_SR,
        T_DEC                       =>  self::T_DEC,
        T_FOR                       =>  self::T_FOR,
        T_INC                       =>  self::T_INC,
        T_NEW                       =>  self::T_NEW,
        T_TRY                       =>  self::T_TRY,
        T_USE                       =>  self::T_USE,
        T_VAR                       =>  self::T_VAR,
        T_CASE                      =>  self::T_CASE,
        T_ECHO                      =>  self::T_ECHO,
        T_ELSE                      =>  self::T_ELSE,
        T_EVAL                      =>  self::T_EVAL,
        T_EXIT                      =>  self::T_EXIT,
        T_FILE                      =>  self::T_FILE,
        T_LINE                      =>  self::T_LINE,
        T_LIST                      =>  self::T_LIST,
        T_NS_C                      =>  self::T_NS_C,
        T_ARRAY                     =>  self::T_ARRAY,
        T_BREAK                     =>  self::T_BREAK,
        T_CLASS                     =>  self::T_CLASS,
        T_CATCH                     =>  self::T_CATCH,
        T_CLONE                     =>  self::T_CLONE,
        T_CONST                     =>  self::T_CONST,
        T_EMPTY                     =>  self::T_EMPTY,
        T_FINAL                     =>  self::T_FINAL,
        T_ISSET                     =>  self::T_ISSET,
        T_PRINT                     =>  self::T_PRINT,
        T_THROW                     =>  self::T_THROW,
        T_UNSET                     =>  self::T_UNSET,
        T_WHILE                     =>  self::T_WHILE,
        T_ELSEIF                    =>  self::T_ELSEIF,
        T_FUNC_C                    =>  self::T_FUNC_C,
        T_GLOBAL                    =>  self::T_GLOBAL,
        T_PUBLIC                    =>  self::T_PUBLIC,
        T_RETURN                    =>  self::T_RETURN,
        T_STATIC                    =>  self::T_STATIC,
        T_STRING                    =>  self::T_STRING,
        T_SWITCH                    =>  self::T_SWITCH,
        T_CLASS_C                   =>  self::T_CLASS_C,
        T_COMMENT                   =>  self::T_COMMENT,
        T_DECLARE                   =>  self::T_DECLARE,
        T_DEFAULT                   =>  self::T_DEFAULT,
        T_DNUMBER                   =>  self::T_DNUMBER,
        T_EXTENDS                   =>  self::T_EXTENDS,
        T_FOREACH                   =>  self::T_FOREACH,
        T_INCLUDE                   =>  self::T_INCLUDE,
        T_LNUMBER                   =>  self::T_LNUMBER,
        T_PRIVATE                   =>  self::T_PRIVATE,
        T_REQUIRE                   =>  self::T_REQUIRE,
        T_FUNCTION                  =>  self::T_FUNCTION,
        T_ABSTRACT                  =>  self::T_ABSTRACT,
        T_INT_CAST                  =>  self::T_INT_CAST,
        T_IS_EQUAL                  =>  self::T_IS_EQUAL,
        T_OR_EQUAL                  =>  self::T_OR_EQUAL,
        T_CONTINUE                  =>  self::T_CONTINUE,
        T_METHOD_C                  =>  self::T_METHOD_C,
        T_OPEN_TAG                  =>  self::T_OPEN_TAG,
        T_SL_EQUAL                  =>  self::T_SL_EQUAL,
        T_SR_EQUAL                  =>  self::T_SR_EQUAL,
        T_VARIABLE                  =>  self::T_VARIABLE,
        T_DIV_EQUAL                 =>  self::T_DIV_EQUAL,
        T_AND_EQUAL                 =>  self::T_AND_EQUAL,
        T_MOD_EQUAL                 =>  self::T_MOD_EQUAL,
        T_MUL_EQUAL                 =>  self::T_MUL_EQUAL,
        T_NAMESPACE                 =>  self::T_NAMESPACE,
        T_XOR_EQUAL                 =>  self::T_XOR_EQUAL,
        T_INTERFACE                 =>  self::T_INTERFACE,
        T_BOOL_CAST                 =>  self::T_BOOL_CAST,
        T_CHARACTER                 =>  self::T_CHARACTER,
        T_CLOSE_TAG                 =>  self::T_CLOSE_TAG,
        T_PROTECTED                 =>  self::T_PROTECTED,
        T_CURLY_OPEN                =>  self::T_CURLY_BRACE_OPEN,
        T_IMPLEMENTS                =>  self::T_IMPLEMENTS,
        T_NUM_STRING                =>  self::T_NUM_STRING,
        T_PLUS_EQUAL                =>  self::T_PLUS_EQUAL,
        T_ARRAY_CAST                =>  self::T_ARRAY_CAST,
        T_BOOLEAN_OR                =>  self::T_BOOLEAN_OR,
        T_INSTANCEOF                =>  self::T_INSTANCEOF,
        T_LOGICAL_OR                =>  self::T_LOGICAL_OR,
        T_UNSET_CAST                =>  self::T_UNSET_CAST,
        T_DOC_COMMENT               =>  self::T_DOC_COMMENT,
        T_END_HEREDOC               =>  self::T_END_HEREDOC,
        T_MINUS_EQUAL               =>  self::T_MINUS_EQUAL,
        T_BOOLEAN_AND               =>  self::T_BOOLEAN_AND,
        T_DOUBLE_CAST               =>  self::T_DOUBLE_CAST,
        T_INLINE_HTML               =>  self::T_INLINE_HTML,
        T_LOGICAL_AND               =>  self::T_LOGICAL_AND,
        T_LOGICAL_XOR               =>  self::T_LOGICAL_XOR,
        T_OBJECT_CAST               =>  self::T_OBJECT_CAST,
        T_STRING_CAST               =>  self::T_STRING_CAST,
        T_DOUBLE_ARROW              =>  self::T_DOUBLE_ARROW,
        T_INCLUDE_ONCE              =>  self::T_INCLUDE_ONCE,
        T_IS_IDENTICAL              =>  self::T_IS_IDENTICAL,
        T_DOUBLE_COLON              =>  self::T_DOUBLE_COLON,
        T_CONCAT_EQUAL              =>  self::T_CONCAT_EQUAL,
        T_IS_NOT_EQUAL              =>  self::T_IS_NOT_EQUAL,
        T_REQUIRE_ONCE              =>  self::T_REQUIRE_ONCE,
        T_BAD_CHARACTER             =>  self::T_BAD_CHARACTER,
        T_HALT_COMPILER             =>  self::T_HALT_COMPILER,
        T_START_HEREDOC             =>  self::T_START_HEREDOC,
        T_STRING_VARNAME            =>  self::T_STRING_VARNAME,
        T_OBJECT_OPERATOR           =>  self::T_OBJECT_OPERATOR,
        T_IS_NOT_IDENTICAL          =>  self::T_IS_NOT_IDENTICAL,
        T_OPEN_TAG_WITH_ECHO        =>  self::T_OPEN_TAG_WITH_ECHO,
        T_IS_GREATER_OR_EQUAL       =>  self::T_IS_GREATER_OR_EQUAL,
        T_IS_SMALLER_OR_EQUAL       =>  self::T_IS_SMALLER_OR_EQUAL,
        T_PAAMAYIM_NEKUDOTAYIM      =>  self::T_DOUBLE_COLON,
        T_ENCAPSED_AND_WHITESPACE   =>  self::T_ENCAPSED_AND_WHITESPACE,
        T_CONSTANT_ENCAPSED_STRING  =>  self::T_CONSTANT_ENCAPSED_STRING,
        T_DOLLAR_OPEN_CURLY_BRACES  =>  self::T_CURLY_BRACE_OPEN,
    );

    /**
     * Mapping between php internal text tokens an php depend numeric tokens.
     *
     * @var array(string=>integer) $literalMap
     */
    protected static $literalMap = array(
        '@'              =>  self::T_AT,
        '/'              =>  self::T_DIV,
        '%'              =>  self::T_MOD,
        '*'              =>  self::T_MUL,
        '+'              =>  self::T_PLUS,
        ':'              =>  self::T_COLON,
        ','              =>  self::T_COMMA,
        '='              =>  self::T_EQUAL,
        '-'              =>  self::T_MINUS,
        '.'              =>  self::T_CONCAT,
        '$'              =>  self::T_DOLLAR,
        '`'              =>  self::T_BACKTICK,
        '\\'             =>  self::T_BACKSLASH,
        ';'              =>  self::T_SEMICOLON,
        '|'              =>  self::T_BITWISE_OR,
        '&'              =>  self::T_BITWISE_AND,
        '~'              =>  self::T_BITWISE_NOT,
        '^'              =>  self::T_BITWISE_XOR,
        '"'              =>  self::T_DOUBLE_QUOTE,
        '?'              =>  self::T_QUESTION_MARK,
        '!'              =>  self::T_EXCLAMATION_MARK,
        '{'              =>  self::T_CURLY_BRACE_OPEN,
        '}'              =>  self::T_CURLY_BRACE_CLOSE,
        '('              =>  self::T_PARENTHESIS_OPEN,
        ')'              =>  self::T_PARENTHESIS_CLOSE,
        '<'              =>  self::T_ANGLE_BRACKET_OPEN,
        '>'              =>  self::T_ANGLE_BRACKET_CLOSE,
        '['              =>  self::T_SQUARED_BRACKET_OPEN,
        ']'              =>  self::T_SQUARED_BRACKET_CLOSE,
        'use'            =>  self::T_USE,
        'null'           =>  self::T_NULL,
        'self'           =>  self::T_SELF,
        'true'           =>  self::T_TRUE,
        'array'          =>  self::T_ARRAY,
        'false'          =>  self::T_FALSE,
        'parent'         =>  self::T_PARENT,
        '__DIR__'        =>  self::T_DIR,
        '__NAMESPACE__'  =>  self::T_NS_C,
    );

    /**
     * The source file instance.
     *
     * @var PHP_Depend_Code_File $sourceFile
     */
    protected $sourceFile = '';

    /**
     * Count of all tokens.
     *
     * @var integer $count
     */
    protected $count = 0;

    /**
     * Internal stream pointer index.
     *
     * @var integer $index
     */
    protected $index = 0;

    /**
     * Prepared token list.
     *
     * @var array(array) $tokens
     */
    protected $tokens = array();

    /**
     * The next free identifier for unknown string tokens.
     *
     * @var integer $_unknownTokenID
     */
    private $_unknownTokenID = 1000;

    /**
     * Constructs a new tokenizer for the given file.
     *
     * @param string $sourceFile A php source file.
     */
    public function __construct($sourceFile = null)
    {
        if ($sourceFile !== null) {
            $this->setSourceFile($sourceFile);
        }
    }

    /**
     * Returns the name of the source file.
     *
     * @return PHP_Depend_Code_File
     */
    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    /**
     * Sets a new php source file.
     *
     * @param string $sourceFile A php source file.
     *
     * @return void
     */
    public function setSourceFile($sourceFile)
    {
        $this->sourceFile = new PHP_Depend_Code_File($sourceFile);
        $this->tokenize();
        $this->sourceFile->setTokens($this->tokens);
    }

    /**
     * Returns the next token or {@link PHP_Depend_Code_TokenizerI::T_EOF} if
     * there is no next token.
     *
     * @return array|integer
     */
    public function next()
    {
        if ($this->index < $this->count) {
            return $this->tokens[$this->index++];
        }
        return self::T_EOF;
    }

    /**
     * Returns the next token type or {@link PHP_Depend_Code_TokenizerI::T_EOF} if
     * there is no next token.
     *
     * @return integer
     */
    public function peek()
    {
        if ($this->index < $this->count) {
            return $this->tokens[$this->index][0];
        }
        return self::T_EOF;
    }

    /**
     * Returns the previous token type or {@link PHP_Depend_Code_TokenizerI::T_BOF}
     * if there is no previous token.
     *
     * @return integer
     */
    public function prev()
    {
        if ($this->index > 1) {
            return $this->tokens[$this->index - 2][0];
        }
        return self::T_BOF;
    }

    /**
     * Tokenizes the content of the source file with {@link token_get_all()} and
     * filters this token stream.
     *
     * @return void
     */
    protected function tokenize()
    {
        $this->tokens = array();
        $this->index  = 0;
        $this->count  = 0;

        // Replace short open tags, it produces bugs.
        $source = $this->sourceFile->getSource();
        $source = str_replace('<?=', '<?php echo ', $source);

        if (version_compare(phpversion(), '5.3.0alpha3') < 0) {
            $tokens = $this->_php53BackslashWorkaround($source);
        } else {
            $tokens = token_get_all($source);
        }

        reset($tokens);

        // The current line number
        $line = 1;

        // Number of skippend lines
        $skippedLines = 0;

        while (($token = current($tokens)) !== false) {
            $newToken = null;
            if (is_string($token)) {
                if (isset(self::$literalMap[$token])) {
                    $newToken = array(self::$literalMap[$token], $token);
                } else {
                    // This should never happen
                    // @codeCoverageIgnoreStart
                    throw new RuntimeException( "Unexpected token '{$token}'." );
                    // @codeCoverageIgnoreEnd
                }
            } else if ($token[0] === T_CLOSE_TAG) {
                // Create a new token instance
                $newToken = array(self::$tokenMap[$token[0]], $token[1]);

                // Fetch next token
                $token = (array) next($tokens);

                // Skipp all non open tags
                while ($token[0] !== T_OPEN_TAG_WITH_ECHO &&
                       $token[0] !== T_OPEN_TAG &&
                       $token[0] !== false) {

                    // Count skipped lines
                    $tokenContent  = (isset($token[1]) ? $token[1] : $token[0]);
                    $skippedLines += substr_count($tokenContent, "\n");

                    $token = (array) next($tokens);
                }

                // Set internal pointer one back
                prev($tokens);
            } else if ($token[0] === T_WHITESPACE) {
                $line += substr_count($token[1], "\n");
            } else {
                $value = strtolower($token[1]);
                if (isset(self::$literalMap[$value])) {
                    $newToken = array(self::$literalMap[$value], $value);
                } else if (isset(self::$tokenMap[$token[0]])) {
                    $newToken = array(self::$tokenMap[$token[0]], $token[1]);
                } else {
                    // This should never happen
                    // @codeCoverageIgnoreStart
                    $newToken = $this->_generateUnknownToken($token[1]);
                    // @codeCoverageIgnoreEnd
                }
            }

            if ($newToken !== null) {
                // Set token line number
                $newToken[2] = $line;

                // Store token in internal ist
                $this->tokens[] = $newToken;

                // Count new line tokens.
                $line += substr_count($newToken[1], "\n") + $skippedLines;
            }

            next($tokens);

            // Rest skipped lines
            $skippedLines = 0;
        }

        $this->count = count($this->tokens);
    }

    /**
     * Workaround to tokenize the backslash namespace separator.
     *
     * @param string $source The raw source code.
     *
     * @return array The tokens.
     */
    private function _php53BackslashWorkaround($source)
    {
        // Replace backslash with valid token
        $source = preg_replace('#\\\\([^"\'`])#i', ':::\\1', $source);
        $tokens = token_get_all($source);

        $result = array();
        for ($i = 0, $c = count($tokens); $i < $c; ++$i) {
            if (is_string($tokens[$i])) {
                $result[] = str_replace(':::', '\\', $tokens[$i]);
            } else if ($tokens[$i][0] !== T_DOUBLE_COLON) {
                $tokens[$i][1] = str_replace(':::', '\\', $tokens[$i][1]);
                $result[]      = $tokens[$i];
            } else if (!isset($tokens[$i + 1]) || $tokens[$i + 1] !== ':') {
                $tokens[$i][1] = str_replace(':::', '\\', $tokens[$i][1]);
                $result[]      = $tokens[$i];
            } else {
                $result[] = '\\';
                ++$i;
            }
        }

        return $result;
    }

    /**
     * Generates a dummy/temp token for unknown string literals.
     *
     * @param string $token The unknown string token.
     *
     * @return array(integer => mixed)
     */
    private function _generateUnknownToken($token)
    {
        return array($this->_unknownTokenID++, $token);
    }
}