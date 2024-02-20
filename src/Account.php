<?php

namespace IVideon;

use IVideon\Exceptions\InvalidArgumentException;

class Account
{
    protected string $login;

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCacheFile(): ?string
    {
        return $this->cacheFile;
    }

    protected string $password;
    protected ?string $cacheFile = null;
    protected string|int|null $userId = null;
    protected ?string $userApiUrl = null;
    protected ?string $token = null;

    /**
     * @param   string       $login
     * @param   string       $password
     * @param   string|null  $cacheFile
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $login, string $password, ?string $cacheFile = null)
    {
        if (empty($login)) {
            throw new InvalidArgumentException('login required', Constants::EXCEPTION_INVALID_LOGIN);
        }
        if (empty($password)) {
            throw new InvalidArgumentException('password required', Constants::EXCEPTION_INVALID_PASSWORD);
        }
        $this->login = $login;
        $this->password = $password;

        if (!empty($cacheFile)) {
            if (!file_exists($cacheFile)) {
                @touch($cacheFile);
            }

            if (!is_writeable($cacheFile)) {
                throw new InvalidArgumentException('cacheFile is not writeable', Constants::EXCEPTION_CACHE_FILE_NOT_WRITEABLE);
            }

            $this->cacheFile = $cacheFile;
            $this->getCachedConfig();
        }
    }

    public function getUserId(): int|string|null
    {
        return $this->userId;
    }

    /**
     * @param null $userId
     *
     * @return Account
     */
    public function setUserId($userId): Account
    {
        $this->userId = $userId;

        return $this;
    }

    protected function getCachedConfig(): void
    {
        $config = @json_decode(file_get_contents($this->cacheFile), true);

        if (empty($config)) {
            return;
        }

        if (!empty($config['access_token'])) {
            $this->setAccessToken($config['access_token']);
        }

        if (!empty($config['userApiUrl'])) {
            $this->setUserApiUrl($config['userApiUrl']);
        }

        if (!empty($config['userId'])) {
            $this->setUserId($config['userId']);
        }
    }

    /**
     * Set User Api Url.
     *
     * @param $url
     *
     * @return $this
     */
    public function setUserApiUrl($url): Account
    {
        $this->userApiUrl = $url;

        return $this;
    }

    /**
     * Set token.
     *
     * @param $token
     *
     * @return $this
     */
    public function setAccessToken($token): Account
    {
        $this->token = $token;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->token;
    }

    public function getUserApiUrl(): ?string
    {
        return $this->userApiUrl;
    }

    protected function storeCacheFile(): void
    {
        if (empty($this->cacheFile)) {
            return;
        }

        if (empty($this->token) || empty($this->userApiUrl) || empty($this->userId)) {
            return;
        }

        $config = [
            'access_token' => $this->getAccessToken(),
            'userApiUrl'   => $this->getUserApiUrl(),
            'userId'       => $this->getUserId(),
        ];

        @file_put_contents($this->cacheFile, json_encode($config));
    }

    public function eraseCacheFile(): ?Account
    {
        if (empty($this->cacheFile)) {
            return null;
        }

        @fclose(@fopen($this->cacheFile, 'w'));
        $this->setAccessToken(null);
        $this->setUserApiUrl(null);

        return $this;
    }

    public function __destruct()
    {
        $this->storeCacheFile();
    }
}
