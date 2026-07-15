<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter: AuthFilter
 * Restricts access to authenticated users. If the user is banned, terminates session and redirects.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->has('user_id')) {
            return redirect()->to('/login');
        }

        // Check if the user is banned in the database
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $session->get('user_id'))->get()->getRow();
        if ($user && (int)$user->is_banned === 1) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Your account has been banned by an administrator.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        return $response;
    }
}
