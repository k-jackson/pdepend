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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Util/FileExtensionFilter.php';

/**
 * Test case for the file extension filter.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Util_FileExtensionFilterTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the extension filter for simple *.txt.
     * 
     * @return void
     */
    public function testFileExtensionFilterWithTxtExtension()
    {
        $it     = new DirectoryIterator(dirname(__FILE__) . '/../_code');
        $filter = new PHP_Depend_Util_FileExtensionFilter(array('txt'));
        
        $result = array();
        foreach ($it as $file) {
            if ($filter->accept($file)) {
                $result[] = $file->getFilename();
            }
        }
        
        sort($result);
        
        $expected = array(
            'invalid_class_with_code.txt',
            'invalid_function1.txt',
            'invalid_function2.txt',
            'not_closed_class.txt',
            'not_closed_function.txt'
        );
        
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Tests the extension filter with multiple allowed file extensions.
     * 
     * @return void
     */
    public function testFileExtensionFilterWithMultipleExtensions()
    {
        $it     = new DirectoryIterator(dirname(__FILE__) . '/../_code');
        $filter = new PHP_Depend_Util_FileExtensionFilter(array('txt', 'inc'));
        
        $result = array();
        foreach ($it as $file) {
            if ($filter->accept($file)) {
                $result[] = $file->getFilename();
            }
        }
        
        sort($result);
        
        $expected = array(
            'function.inc',
            'invalid_class_with_code.txt',
            'invalid_function1.txt',
            'invalid_function2.txt',
            'not_closed_class.txt',
            'not_closed_function.txt'
        );
        
        $this->assertEquals($expected, $result);
    }
}