<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequestRequest;
use App\Http\Requests\UpdateServiceRequestStatusRequest;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = ServiceRequest::query()->latest('created_at');
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $requestType = (string) $request->query('request_type', '');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('requester_name', 'like', '%'.$search.'%')
                    ->orWhere('requester_email', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if (array_key_exists($status, ServiceRequest::statuses())) {
            $query->where('status', $status);
        }

        if (array_key_exists($requestType, ServiceRequest::types())) {
            $query->where('request_type', $requestType);
        }

        return view('service-requests.index', [
            'serviceRequests' => $query->paginate(10)->withQueryString(),
            'statuses' => ServiceRequest::statuses(),
            'types' => ServiceRequest::types(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'request_type' => $requestType,
            ],
        ]);
    }

    public function create(): View
    {
        return view('service-requests.create', [
            'types' => ServiceRequest::types(),
        ]);
    }

    public function store(StoreServiceRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = ServiceRequest::STATUS_NEW;
        $serviceRequest = ServiceRequest::query()->create($data);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Solicitud registrada correctamente.');
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        return view('service-requests.show', [
            'serviceRequest' => $serviceRequest,
            'statuses' => ServiceRequest::statuses(),
            'types' => ServiceRequest::types(),
        ]);
    }

    public function updateStatus(
        UpdateServiceRequestStatusRequest $request,
        ServiceRequest $serviceRequest,
    ): RedirectResponse {
        $serviceRequest->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Estado actualizado correctamente.');
    }
}
