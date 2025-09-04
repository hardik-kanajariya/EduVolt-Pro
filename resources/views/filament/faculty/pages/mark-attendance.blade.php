<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Mark Attendance</h2>
            
            <!-- Class and Date Selection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                    <select wire:model.live="selectedClass" wire:change="loadStudents" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose a class...</option>
                        @foreach($this->getClassOptions() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" wire:model.live="selectedDate" wire:change="loadStudents"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div class="flex items-end">
                    <button wire:click="markAttendance" 
                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Mark Attendance
                    </button>
                </div>
            </div>
        </div>

        <!-- Students List -->
        @if($students->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Students ({{ $students->count() }})</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Roll No.
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Remarks
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $student->admission_number }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $student->roll_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:change="updateAttendanceStatus({{ $student->id }}, $event.target.value)"
                                    wire:model="attendanceData.{{ $student->id }}.status"
                                    class="border-gray-300 rounded text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                    wire:model="attendanceData.{{ $student->id }}.remarks"
                                    wire:blur="updateAttendanceRemarks({{ $student->id }}, $event.target.value)"
                                    placeholder="Add remarks..."
                                    class="w-full border-gray-300 rounded text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif($selectedClass)
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">No students found for the selected class and date.</p>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">Please select a class and date to load students.</p>
        </div>
        @endif
    </div>
</x-filament-panels::page>
