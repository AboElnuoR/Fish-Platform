<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use App\Models\Company;
use App\Models\CompanyBank;
use App\Models\CompanyClntSplr;
use App\Models\CompanyMembership;
use App\Models\Governorate;
use App\Models\HSCode;
use App\Models\Locality;
use App\Models\Membership;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $governorates = Governorate::all()->pluck('Governorate_Name_A', 'Governorate_ID');
        $locals = Locality::all()->pluck('Locality_Name_A', 'Locality_ID');
        $villages = Village::all()->pluck('Village_Name_A', 'Village_ID');
        $types = ActivityType::where('ActivityTypeGroup_ID', '41')->get()->pluck('AName', 'ActivityType_ID');
        $banks = CompanyBank::all()->pluck('Bank_Name_A', 'Bank_ID');
        $memberships = CompanyMembership::all()->pluck('MemberNameAr', 'Member_ID');
        $hscodes = HSCode::all()->pluck('HS_Aname', 'HSCode_ID');
        $clntsplrs = CompanyClntSplr::where('CompanyType', 1)->get();

        $impClnts = new Collection;
        $clntsplrs = $clntsplrs->filter(function ($clntsplr) use (&$impClnts) {
            if ($clntsplr->Type_ID == 1) {
                return true;
            }
            $impClnts->push($clntsplr);
        });

        $clntsplrs = $clntsplrs->pluck('ClntSplr_Name', 'ClntSplr_ID');
        $impClnts = $impClnts->pluck('ClntSplr_Name', 'ClntSplr_ID');

        return view('companies.create', compact(
            'governorates',
            'locals',
            'villages',
            'types',
            'banks',
            'memberships',
            'hscodes',
            'clntsplrs',
            'impClnts'
        ));
    }

    /**
     * Specify the form's rules.
     *
     * @return array
     */
    private function rules()
    {
        return [
            'FishCompanyName' => 'required|string',
            'TradeMark' => 'sometimes|nullable|string',
            'EYear' => 'sometimes|nullable|numeric',
            'EmpCount' => 'sometimes|nullable|numeric',
            'RegNo' => 'sometimes|nullable|numeric',
            'Activity' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'FishCompanyType_ID' => '1',
            'EntryUser' => auth()->id(),
            'UpdateUser' => auth()->id(),
        ];
        $data += $request->validate($this->rules());
        if (!empty(request('ActivityType_ID'))) {
            \Validator::make(request('ActivityType_ID'), [
                'ActivityType_ID.*' => [
                    'sometimes',
                    Rule::exists('activitytype')->where('ActivityTypeGroup_ID', '41'),
                ],
            ]);
        }

        try {
            \DB::transaction(function () use ($data) {
                $company = Company::create($data);
                $company->activitytypes()->sync(request('ActivityType_ID'));
                session(compact('company'));
            });

            $success = 'تم انشاء مستلزمات الإنتاج بنجاح';
        } catch (\Exception $e) {
            $error = 'حدث خطأ يرجى المحاولة مرة أخرى';
        }

        return back()->with(compact('success', 'error'));
    }

    /**
     * Specify the branch form's rules.
     *
     * @return array
     */
    private function branchRules()
    {
        return [
            'Governorate_ID' => 'required|exists:governorate',
            'Locality_ID' => 'required|exists:locality',
            'Village_ID' => request('Village_ID') ? 'required|exists:village' : '',
            'Address' => 'sometimes|nullable|string',
            'Mob' => 'sometimes|nullable|numeric',
            'Tel' => 'required|numeric',
            'Fax' => 'sometimes|nullable|numeric',
            'Email' => 'sometimes|nullable|string|email',
            'Web' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Add branch informstion to company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function addBranch(Request $request, Company $company)
    {
        $data = $request->validate($this->branchRules());
        $company->branches()->create($data);

        session(compact('company'));
        $success = 'تم انشاء الفرع بنجاح';
        return back()->with(compact('success'));
    }

    /**
     * Specify the manager form's rules.
     *
     * @return array
     */
    private function managerRules()
    {
        return [
            'EmpName' => 'required|string',
            'Job' => 'required|string',
            'Mob' => 'required|numeric',
            'Email' => 'sometimes|nullable|string|email',
        ];
    }

    /**
     * Add manager informstion to company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function addManager(Request $request, Company $company)
    {
        $data = $request->validate($this->managerRules());
        $company->managers()->create($data);

        session(compact('company'));
        $success = 'تم انشاء الموظف بنجاح';
        return back()->with(compact('success'));
    }

    /**
     * Specify the bank form's rules.
     *
     * @return array
     */
    private function banksRules()
    {
        return [
            'Bank_ID.*' => 'required|exists:bank,Bank_ID',
        ];
    }

    /**
     * Add banks informstion to company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function addBanks(Request $request, Company $company)
    {
        $data = $request->validate($this->banksRules());
        $company->banks()->sync($data['Bank_ID']);

        session(compact('company'));
        $success = 'تم تحديد البنوك بنجاح';
        return back()->with(compact('success'));
    }

    /**
     * Specify the membership form's rules.
     *
     * @return array
     */
    private function membershipRules()
    {
        return [
            'Member_ID.*' => 'required|exists:member,Member_ID',
        ];
    }

    /**
     * Add membership informstion to company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function addMembership(Request $request, Company $company)
    {
        $data = $request->validate($this->membershipRules());
        $company->memberships()->sync($data['Member_ID']);

        session(compact('company'));
        $success = 'تم تحديد العضويات بنجاح';
        return back()->with(compact('success'));
    }

    /**
     * Specify the hSCodes form's rules.
     *
     * @return array
     */
    private function hSCodeRules()
    {
        return [
            'HSCode_ID.*' => 'required|exists:hscode,HSCode_ID',
            'ClntSplr_ID.*' => 'required|exists:clntsplr,ClntSplr_ID',
        ];
    }

    /**
     * Add hSCode informstion to company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function addHSCode(Request $request, Company $company)
    {
        $data = $request->validate($this->hSCodeRules());
        try {
            \DB::transaction(function () use ($company, $request) {
                // dd($request->only('HSCode_ID'));
                $company->hSCodes()->sync($request->HSCode_ID);
                $company->clntSplrs()->sync($request->ClntSplr_ID);
                $this->update($request, $company);
            });
            $success = 'تم تحديد مجموعة الشركات بنجاح';
        } catch (\Exception $e) {
            throw $e;
            $error = 'حدث خطأ يرجى المحاولة مرة أخرى';
        }

        session(compact('company'));
        return back()->with(compact('success', 'error'));
    }

    /**
     * Specify the Source form's rules.
     *
     * @return array
     */
    private function sourceRules()
    {
        return [
            'SourceS1' => 'sometimes|nullable|string',
            'Counts1' => 'sometimes|nullable|string',
            'SourceS2' => 'sometimes|nullable|string',
            'Counts2' => 'sometimes|nullable|string',
            'SourceS3' => 'sometimes|nullable|string',
            'Counts3' => 'sometimes|nullable|string',
            'SourceS4' => 'sometimes|nullable|string',
            'Counts4' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Add Source informstion to Company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function addSource(Request $request, Company $company)
    {
        $data = $request->validate($this->sourceRules());
        $company->sources()->updateOrCreate([], $data);
        session(compact('company'));

        $success = 'تم انشاء بيانات مستلزمات التشغيل بنجاح';
        return back()->with(compact('success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Specify the update form's rules.
     *
     * @return array
     */
    private function updateRules()
    {
        return [
            'ShareHoldr' => 'sometimes|nullable|string',
            'ShareHoldrFrgn' => 'sometimes|nullable|string',
            'ComGroup' => 'sometimes|nullable|string',
            'tradMarks' => 'sometimes|nullable|string',
            'WrkArea' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $data = $request->validate($this->updateRules());
        $company->update($data);

        session(compact('company'));
        $success = 'تم إضافة المساهمون بنجاح';
        return back()->with(compact('success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
        session()->forget('company');
        $success = 'تم حذف مستلزمات الإنتاج بنجاح';
        return back()->with(compact('success'));
    }
}
