<?php
/**
 * Copyright (c) 2026 MageSetu. All rights reserved.
 *
 * @package    MageSetu_AiBase
 * @license    https://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

declare(strict_types=1);

namespace MageSetu\AiBase\Test\Unit\Model\Config\Converter;

use MageSetu\AiBase\Model\Config\Converter\AiModelRegistry;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MageSetu\AiBase\Model\Config\Converter\AiModelRegistry
 */
class AiModelRegistryTest extends TestCase
{
    public function testConvertsProviderAndModelMetadata(): void
    {
        $document = new \DOMDocument();
        $document->loadXML(<<<'XML'
<config>
    <provider code="test" label="Test Provider">
        <model name="test-model" label="Test Model">
            <type>chat</type>
            <supports_reasoning_effort>true</supports_reasoning_effort>
            <supports_verbosity>false</supports_verbosity>
            <supports_temperature>true</supports_temperature>
            <capabilities>
                <capability>chat</capability>
                <capability>reasoning</capability>
            </capabilities>
            <token_param>max_tokens</token_param>
            <default_reasoning_effort>medium</default_reasoning_effort>
            <context_window>8192</context_window>
        </model>
    </provider>
</config>
XML);

        $result = (new AiModelRegistry())->convert($document);

        $this->assertSame('Test Provider', $result['test']['label']);
        $this->assertSame([
            'name' => 'test-model',
            'label' => 'Test Model',
            'type' => 'chat',
            'capabilities' => ['chat', 'reasoning'],
            'token_param' => 'max_tokens',
            'supports_reasoning_effort' => true,
            'reasoning_effort_values' => [],
            'default_reasoning_effort' => 'medium',
            'supports_verbosity' => false,
            'supports_temperature' => true,
            'context_window' => 8192,
            'dimensions' => null,
        ], $result['test']['models']['test-model']);
    }

    public function testUsesFallbackLabelsAndNullForMissingOptionalFields(): void
    {
        $document = new \DOMDocument();
        $document->loadXML(<<<'XML'
<config>
    <provider code="test">
        <model name="test-model">
            <type>embedding</type>
            <supports_reasoning_effort>false</supports_reasoning_effort>
            <supports_verbosity>false</supports_verbosity>
            <supports_temperature>false</supports_temperature>
            <capabilities><capability>embedding</capability></capabilities>
        </model>
    </provider>
</config>
XML);

        $model = (new AiModelRegistry())->convert($document)['test']['models']['test-model'];

        $this->assertSame('test-model', $model['label']);
        $this->assertNull($model['token_param']);
        $this->assertNull($model['context_window']);
        $this->assertNull($model['dimensions']);
    }
}
