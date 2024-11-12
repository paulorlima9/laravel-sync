<?php

namespace Aerni\Sync;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PathGenerator
{
    public function localPath(string $path): string
    {
        return base_path($path);
    }

    public function remotePath(array $remote, string $path): string
    {
        if (Str::endsWith($path, '/')) {
            $fullPath = $this->joinPaths($remote['root'], $path);
        } else {
            $fullPath = $remote['root'].'/ --copy-links';
        }

        if ($this->remoteHostEqualsLocalHost($remote['host'])) {
            return $fullPath;
        }

        return "{$remote['user']}@{$remote['host']}:$fullPath";
    }

    protected function joinPaths(): string
    {
        $paths = [];

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', implode('/', $paths));
    }

    protected function remoteHostEqualsLocalHost(string $remoteHost): bool
    {
        $ip = Http::get('https://api.ipify.org/?format=json')->json('ip');

        if ($ip !== $remoteHost) {
            return false;
        }

        return true;
    }
}
