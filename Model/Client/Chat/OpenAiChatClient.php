<?php
/**
 * Copyright (c) 2026 MageSetu. All rights reserved.
 *
 * @package    MageSetu_AiBase
 * @author     MageSetu
 * @copyright  Copyright (c) 2026 MageSetu.
 * @license    https://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link       https://github.com/MageSetu/module-ai-base
 */

declare(strict_types=1);

namespace MageSetu\AiBase\Model\Client\Chat;

use MageSetu\AiBase\Exception\AiClientException;

/**
 * OpenAI reasoning / chat-completion client.
 *
 * Example:
 *
 *   $client = $this->openAiChatClientFactory->create([
 *       'apiKey' => 'sk-...',
 *       'model'  => 'gpt-4o',
 *   ]);
 *
 * @since 1.0.0
 */
class OpenAiChatClient extends AbstractChatClient
{
    /** Model code */
    protected const CODE  = 'openai';

    /** Model label */
    protected const LABEL = 'OpenAI';

    /** Default base url to connect via API */
    public const DEFAULT_BASE_URL = 'https://api.openai.com';

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return self::CODE;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return self::LABEL;
    }

    /**
     * @inheritDoc
     */
    public function isAvailable(): bool
    {
        $authHeader = $this->apiKey ? $this->generateAuthHeader($this->apiKey) : [];

        try {
            $response = $this->getJson(
                $this->baseUrl . '/v1/models',
                $authHeader
            );

            if (!isset($response['data'])) {
                throw new AiClientException('[openai] Unable to connect with the client.');
            }

            $models = array_column($response['data'], 'id');
            if (!in_array($this->model, $models)) {
                throw new AiClientException(sprintf('[openai] Model %s is not available.', $this->model));
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->debug('[openai][chat] isAvailable failed: ' . $e->getMessage());
            return false;
        }
    }
}
