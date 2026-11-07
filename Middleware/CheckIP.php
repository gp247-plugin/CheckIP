<?php

namespace App\GP247\Plugins\CheckIP\Middleware;

use Closure;

class CheckIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ipList = $this->ipList();
        $ipsAllow = $this->ipsAllow();
        $ipsDeny = $this->ipsDeny();
        // Check if any IP in $ipList matches any IP in $ipsAllow, or if allow all ('*'), or is localhost
        if (
            in_array('127.0.0.1', $ipList) || 
            in_array('::1', $ipList) || 
            ($ipsAllow && (count(array_intersect($ipList, $ipsAllow)) > 0 || in_array('*', $ipsAllow)))
        ) {
            return $next($request);
        } 

        if ($ipsDeny && (count(array_intersect($ipList, $ipsDeny)) > 0 || in_array('*', $ipsDeny))) {
            // Find the first matching IP between $ipList and $ipsDeny
            $flatIpList = [];
            // Flatten $ipList to a simple array of IPs
            foreach ($ipList as $item) {
                if (is_array($item)) {
                    foreach ($item as $ip) {
                        $flatIpList[] = $ip;
                    }
                } elseif (!empty($item)) {
                    $flatIpList[] = $item;
                }
            }
            $firstBlockedIp = null;
            foreach ($flatIpList as $ip) {
                if (in_array($ip, $ipsDeny)) {
                    $firstBlockedIp = $ip;
                    break;
                }
            }
            // If no specific IP is found, check for the deny all ('*') case
            if (!$firstBlockedIp && in_array('*', $ipsDeny)) {
                $firstBlockedIp = '*';
            }
            abort(403, 'Your IP ' . $firstBlockedIp . ' blocked');
        }


        return $next($request);
    }

    /**
     * Resolve the one client IP used for allow/deny matching.
     *
     * WHY: only Request::ip() is the visitor — the nearest address that is not a
     * proxy declared in TRUSTED_PROXIES (config/trustedproxy.php). Request::ips()
     * also returns every untrusted entry of X-Forwarded-For, including whatever the
     * visitor wrote at its start (a CDN only appends), so matching against it let
     * a forged "127.0.0.1" or allow-listed address pass even behind a correctly
     * declared Cloudflare. Forwarded headers are never read directly either.
     *
     * @return array<int, string> The client IP, or an empty list when unknown.
     *
     * @aidlc-unit check-ip-plugin
     * @aidlc-story US-check-ip-plugin-real-client-ip
     */
    protected function ipList()
    {
        $ip = (string) request()->ip();

        return $ip === '' ? [] : [$ip];
    }

    /**
     * Allowed IPs configured on the CheckIP screen.
     *
     * @return array<int, string>
     *
     * @aidlc-unit check-ip-plugin
     * @aidlc-story US-check-ip-plugin-real-client-ip
     */
    protected function ipsAllow(): array
    {
        return \App\GP247\Plugins\CheckIP\Models\CheckIPAccess::getIpsAllow();
    }

    /**
     * Denied IPs configured on the CheckIP screen.
     *
     * @return array<int, string>
     *
     * @aidlc-unit check-ip-plugin
     * @aidlc-story US-check-ip-plugin-real-client-ip
     */
    protected function ipsDeny(): array
    {
        return \App\GP247\Plugins\CheckIP\Models\CheckIPAccess::getIpsDeny();
    }
}
