<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Registration PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 10px;
            color: #333;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
        }

        .table-title {
            background: #004080;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 14px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 2px solid #000;
        }

        .header img {
            height: 70px;
            width: auto;
        }

        .header-title {
            flex-grow: 1;
            text-align: center;
            color: blue;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <img src="{{ public_path('images/HRDlogo.png') }}" alt="HRD Logo">
        </div>
        <br>
        <div class="header-title">
            Information and Library Network (INFLIBNET) Centre, Gandhinagar <br>
            <span style="font-size: 14px; font-weight: normal;">(An Autonomous Inter-University Centre of UGC)</span>
        </div>
    </div>

    <h2 style="text-align: center; margin-top: 10px;">Participant Registration Details</h2>
    <table class="table-bordered">
        <!-- Personal Details -->
        <tr class="table-title">
            <th colspan="2">Personal Details</th>
        </tr>
        @if (!empty($participant->fname))
            <tr>
                <td>Full Name:</td>
                <td>{{ $participant->prefix ?? '' }} {{ $participant->fname ?? '' }} {{ $participant->lname ?? '' }}
                </td>
            </tr>
        @endif
        @if (!empty($participant->email))
            <tr>
                <td>Email:</td>
                <td>{{ $participant->email }}</td>
            </tr>
        @endif
        @if (!empty($participant->mobile))
            <tr>
                <td>Mobile:</td>
                <td>{{ $participant->mobile }}</td>
            </tr>
        @endif
        @if (!empty($participant->address))
            <tr>
                <td>Address:</td>
                <td>{{ $participant->address }}</td>
            </tr>
        @endif
        @if (!empty($participant->gender))
            <tr>
                <td>Gender:</td>
                <td>{{ $participant->gender }}</td>
            </tr>
        @endif
        @if (!empty($registration->category))
            <tr>
                <td>Category:</td>
                <td>{{ $registration->category }}</td>
            </tr>
        @endif
        @if (!empty($participant->city))
            <tr>
                <td>City:</td>
                <td>{{ $participant->city }}</td>
            </tr>
        @endif
        @if (!empty($participant->pincode))
            <tr>
                <td>Pincode:</td>
                <td>{{ $participant->pincode }}</td>
            </tr>
        @endif
        @if (!empty($participant->district))
            <tr>
                <td>District:</td>
                <td>{{ $participant->district }}</td>
            </tr>
        @endif
        @if (!empty($participant->state))
            <tr>
                <td>State:</td>
                <td>{{ $participant->state }}</td>
            </tr>
        @endif
        @if (!empty($participant->county))
            <tr>
                <td>County:</td>
                <td>{{ $participant->county }}</td>
            </tr>
        @endif

        <!-- Social & Professional Details -->
        @if (
            !empty($participant->facebook) ||
                !empty($participant->linkedin) ||
                !empty($participant->twitter) ||
                !empty($participant->orcid))
            <tr class="table-title">
                <th colspan="2">Social &amp; Professional Details</th>
            </tr>
            @if (!empty($participant->facebook))
                <tr>
                    <td>Facebook:</td>
                    <td>{{ $participant->facebook }}</td>
                </tr>
            @endif
            @if (!empty($participant->linkedin))
                <tr>
                    <td>LinkedIn:</td>
                    <td>{{ $participant->linkedin }}</td>
                </tr>
            @endif
            @if (!empty($participant->twitter))
                <tr>
                    <td>Twitter:</td>
                    <td>{{ $participant->twitter }}</td>
                </tr>
            @endif
            @if (!empty($participant->orcid))
                <tr>
                    <td>ORCID:</td>
                    <td>{{ $participant->orcid }}</td>
                </tr>
            @endif
        @endif

        <!-- Work Details -->
        @if (!empty($participant->designation) || !empty($participant->institute_name))
            <tr class="table-title">
                <th colspan="2">Work Details</th>
            </tr>
            @if (!empty($participant->designation))
                <tr>
                    <td>Designation:</td>
                    <td>{{ $participant->designation }}</td>
                </tr>
            @endif
            @if (!empty($participant->institute_name))
                <tr>
                    <td>Organization / Institute Name:</td>
                    <td>{{ $participant->institute_name }}</td>
                </tr>
            @endif
        @endif

        <!-- Passport Details -->
        @if (
            !empty($registration->passno) ||
                !empty($registration->nation) ||
                !empty($registration->passport_valid_until) ||
                !empty($registration->place_of_birth))
            <tr class="table-title">
                <th colspan="2">Passport Details</th>
            </tr>
            @if (!empty($registration->passno))
                <tr>
                    <td>Passport Number:</td>
                    <td>{{ $registration->passno }}</td>
                </tr>
            @endif
            @if (!empty($registration->nation))
                <tr>
                    <td>Nationality:</td>
                    <td>{{ $registration->nation }}</td>
                </tr>
            @endif
            @if (!empty($registration->passv))
                <tr>
                    <td>Passport Valid Until:</td>
                    <td>{{ $registration->passv }}</td>
                </tr>
            @endif
            @if (!empty($registration->pob))
                <tr>
                    <td>Place of Birth:</td>
                    <td>{{ $registration->pob }}</td>
                </tr>
            @endif
        @endif

        @if (!empty($registration) && isset($registration->accommodation))
            <tr class="table-title">
                <th colspan="2">Accommodation Details</th>
            </tr>
            <tr>
                <td>Accommodation:</td>
                <td>
                    {{ $registration->accommodation === 'Yes' ? 'Accommodation Provided' : 'Not Provided' }}
                </td>
            </tr>
        @endif



        @if (!empty($registration->reg_type))
            <tr class="table-title">
                <th colspan="2">Registration Details</th>
            </tr>
            <tr>
                <td>Registration Type:</td>
                <td>
                    @if ($registration->reg_type == '1')
                        Individual
                    @elseif($registration->reg_type == '2')
                        Group
                        @if (!empty($registration->reg_group_name))
                            ({{ $registration->reg_group_name }})
                        @endif
                    @endif
                </td>
            </tr>
        @endif


        <!-- Payment Details -->
        @if (!empty($registration->payment))
            <tr class="table-title">
                <th colspan="2">Payment Details</th>
            </tr>
            <tr>
                <td>Payment Type:</td>
                <td>{{ $registration->payment == 1 ? 'Online' : 'Offline' }}</td>
            </tr>
            @if (!empty($registration->pbank))
                <tr>
                    <td>Bank Name:</td>
                    <td>{{ $registration->pbank }}</td>
                </tr>
            @endif
            @if (!empty($registration->pdate))
                <tr>
                    <td>Date:</td>
                    <td>{{ $registration->pdate }}</td>
                </tr>
            @endif
            @if (!empty($registration->pamt))
                <tr>
                    <td>Amount:</td>
                    <td>{{ $registration->pamt }}</td>
                </tr>
            @endif
        @endif

        <!-- Travel Details -->
        @if (!empty($registration->mode) || !empty($registration->adate) || !empty($registration->rdate))
            <tr class="table-title">
                <th colspan="2">Travel Details</th>
            </tr>
            @if (!empty($registration->mode))
                <tr>
                    <td>Mode of Travel:</td>
                    <td>{{ $registration->mode }}</td>
                </tr>
            @endif
            @if (!empty($registration->adate))
                <tr>
                    <td>Arrival Date &amp; Time:</td>
                    <td>{{ $registration->adate }} {{ $registration->arrival_time ?? '' }}</td>
                </tr>
            @endif
            @if (!empty($registration->rdate))
                <tr>
                    <td>Return Date &amp; Time:</td>
                    <td>{{ $registration->rdate }} {{ $registration->return_time ?? '' }}</td>
                </tr>
            @endif
        @endif
    </table>

    <div class="footer">
        <p>© 2025 INFLIBNET. All Rights Reserved.</p>
    </div>
</body>

</html>
