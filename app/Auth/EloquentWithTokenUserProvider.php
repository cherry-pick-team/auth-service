<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class EloquentWithTokenUserProvider extends EloquentUserProvider
{
    /**
     * The Eloquent user tokens model.
     *
     * @var string
     */
    protected $modelTokens;

    /**
     * The user id field in Eloquent user tokens model.
     *
     * @var string
     */
    protected $modelTokensFieldUserId = 'user_id';

    /**
     * The token field in Eloquent user tokens model.
     *
     * @var string
     */
    protected $modelTokensFieldToken = 'token';

    /**
     * The token field in credentials.
     *
     * @var string
     */
    protected $tokenCredential = 'api_token';

    /**
     * Create a new database user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher $hasher
     * @param  string $model
     * @param  string $modelTokens
     * @return void
     */
    public function __construct(HasherContract $hasher, $model, $modelTokens)
    {
        parent::__construct($hasher, $model);
        $this->modelTokens = $modelTokens;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        if (array_key_exists($this->tokenCredential, $credentials)) {
            $query = $this->createModelTokens()->newQuery()
                ->where($this->modelTokensFieldToken, $credentials[$this->tokenCredential]);

            $tokenRow = $query->first();

            if ($tokenRow) {
                return $this->retrieveById($tokenRow->getOriginal($this->modelTokensFieldUserId));
            }

            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Create a new instance of the tokens model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModelTokens()
    {
        $class = '\\' . ltrim($this->modelTokens, '\\');

        return new $class;
    }

    /**
     * Gets the name of the Eloquent user tokens model.
     *
     * @return string
     */
    public function getModelTokens()
    {
        return $this->modelTokens;
    }

    /**
     * Sets the name of the Eloquent user tokens model.
     *
     * @param  string $modelTokens
     * @return $this
     */
    public function setModelTokens($modelTokens)
    {
        $this->modelTokens = $modelTokens;

        return $this;
    }
}