<?php

namespace Tests\Functional;

class ClassBuilderTest extends BaseTestCase
{
    public function providerProperties()
    {
        return [
            [
                'public $bob = true; // something'.PHP_EOL.PHP_EOL.'    protected $bill;',
                [
                    0 => [
                        'name' => 'bob',
                        'scope' => 'public',
                        'type' => 'bool',
                    ],
                    1 => [
                        'name' => 'bill',
                        'scope' => 'protected',
                        'type' => '',
                    ],
                ],
            ],
            [
                'public $no_space_between_name_and_value=true; // something'.PHP_EOL.PHP_EOL.'    protected $snail_case_variable;',
                [
                    0 => [
                        'name' => 'no_space_between_name_and_value',
                        'scope' => 'public',
                        'type' => 'bool',
                    ],
                    1 => [
                        'name' => 'snail_case_variable',
                        'scope' => 'protected',
                        'type' => '',
                    ]
                ],
            ],
            [
                PHP_EOL.PHP_EOL.'    protected $lostOfEmptyLines;',
                [
                    0 => [
                        'name' => 'lostOfEmptyLines',
                        'scope' => 'protected',
                        'type' => '',
                    ],
                ]
            ],
            [
                '// comment'.PHP_EOL.PHP_EOL.'    protected $commentOnItsOwnLine;',
                [
                    0 => [
                        'name' => 'commentOnItsOwnLine',
                        'scope' => 'protected',
                        'type' => '',

                    ],
                ]
            ],
            [
                '/*'.PHP_EOL.'comment body'.PHP_EOL.'*/'.PHP_EOL.'    protected $multiLineComments;',
                [
                    0 => [
                        'name' => 'multiLineComments',
                        'scope' => 'protected',
                        'type' => '',
                    ],
                ]
            ],
            [
                '/**'.PHP_EOL.'comment body'.PHP_EOL.'with $variable'.PHP_EOL.'*/'.PHP_EOL.'    protected $multiLineCommentsDocBlockStyle;',
                [
                    0 => [
                        'name' => 'multiLineCommentsDocBlockStyle',
                        'scope' => 'protected',
                        'type' => '',
                    ],
                ]
            ],
            [
                ';',
                [
                ],
            ],
            [
                'public $bob = 1;',
                [
                    0 => [
                        'name' => 'bob',
                        'scope' => 'public',
                        'type' => 'int',
                    ],
                ],
            ],
            [
                'public $bob = 10050;',
                [
                    0 => [
                        'name' => 'bob',
                        'scope' => 'public',
                        'type' => 'int',
                    ],
                ],
            ],
            [
                'public $bob = 1.0;',
                [
                    0 => [
                        'name' => 'bob',
                        'scope' => 'public',
                        'type' => 'float',
                    ],
                ],
            ],
            [
                'public $bob = 10.005;',
                [
                    0 => [
                        'name' => 'bob',
                        'scope' => 'public',
                        'type' => 'float',
                    ],
                ],
            ],
            [
                'DiscountType $type,',
                [
                    0 => [
                        'name' => 'type',
                        'scope' => 'protected',
                        'type' => '', //'DiscountType',
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider providerProperties
     */
    public function testParsingProperties($codeString, $expected)
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('POST', '/api/php/parse')
            ->withParsedBody(['properties' => $codeString]);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertEquals(['properties' => $expected], $responseData);
    }

    public function providerPropertiesWithTypes()
    {
        return [
            [
                [
                    '0' => [
                        'scope' => "private",
                        'property' => 'bob',
                        'type' => 's',
                        'enabled' => "1",
                    ],
                    '1' => [
                        'scope' => "private",
                        'property' => 'bill',
                        'type' => 'd',
                        'enabled' => "1",
                    ]
                ],
                <<<'CODE'
/**
 * With bob
 *
 * @param string
 * @return self
 */
public function withBob(string $bob): self
{
    $instance = clone $this;
    $instance->bob = $bob;

    return $instance;
}

/**
 * Get bob
 *
 * @return string
 */
public function getBob(): string
{
    return $this->bob;
}

/**
 * With bill
 *
 * @param DateTimeImmutable
 * @return self
 */
public function withBill(DateTimeImmutable $bill): self
{
    $instance = clone $this;
    $instance->bill = $bill;

    return $instance;
}

/**
 * Get bill
 *
 * @return DateTimeImmutable
 */
public function getBill(): DateTimeImmutable
{
    return $this->bill;
}

/**
 * Don't allow anything to be set
 *
 * @param string $name
 * @param mixed $value
 */
public function __set($name, $value)
{
    throw new \InvalidArgumentException(self::class.' is immutable');
}

CODE
            ],
        ];
    }

    /**
     * @dataProvider providerPropertiesWithTypes
     */
    public function testCodeGenerating($properties, $expected)
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('POST', '/api/php/build')
            ->withParsedBody(['properties' => $properties, 'version' => '8']);
        $response = $app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertEquals(['code' => $expected], $responseData);
    }
}
