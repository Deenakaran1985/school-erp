@extends('layouts.app')
@section('title','Notifications')
@section('page_title','Push Notifications')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- Send Panel -->
  @can('notification.send')
    <div class="lg:col-span-1">
      <div class="bg-white rounded-2xl border border-slate-200 p-5 sticky top-24">
        <h3 class="font-semibold text-slate-700 mb-4">🔔 Send Notification</h3>
        <form method="POST" action="{{ route('admin.notifications.send') }}"
          class="space-y-3">
          @csrf

          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Title *</label>
            <input type="text" name="title" required
              placeholder="Notification title"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"/>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Message *</label>
            <textarea name="body" rows="3" required
              placeholder="Notification body..."
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm"></textarea>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Send To *</label>
            <select name="target_role" required
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
              <option value="all">🌐 Everyone (all roles)</option>
              <option value="parent">👨‍👩‍👧 All Parents</option>
              <option value="student">🎓 All Students</option>
              <option value="teacher">👩‍🏫 All Teachers</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">
              Class Filter <span class="text-slate-400">(optional — for parents of a specific class)</span>
            </label>
            <select name="target_class_id"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
              <option value="">All Classes</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}">Class {{ $class->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">
              Type <span class="text-slate-400">(for Flutter routing)</span>
            </label>
            <select name="type"
              class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm">
              @foreach([
                'general'=>'General Announcement',
                'exam_schedule'=>'Exam Schedule',
                'fee_reminder'=>'Fee Reminder',
                'holiday'=>'Holiday Notice',
                'event'=>'Event / Function',
                'emergency'=>'Emergency',
              ] as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <button type="submit"
            class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm">
            🚀 Send Now
          </button>
        </form>
      </div>
    </div>
  @endcan

  <!-- Notification History -->
  <div class="lg:col-span-2 space-y-3">
    <h3 class="font-semibold text-slate-700">📬 Sent History</h3>
    @forelse($notifications as $notif)
      <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-sm font-semibold text-slate-800">{{ $notif->title }}</span>
              <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">
                {{ $notif->type }}
              </span>
            </div>
            <p class="text-sm text-slate-500">{{ $notif->body }}</p>
            <div class="flex gap-4 mt-2 text-xs text-slate-400">
              <span>To: <strong>{{ ucfirst($notif->target_role) }}</strong></span>
              <span>Sent: <strong class="text-green-600">{{ $notif->sent_count }}</strong> devices</span>
              <span>By: {{ $notif->sentBy->name }}</span>
              <span>{{ $notif->sent_at?->diffForHumans() }}</span>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400">
        No notifications sent yet.
      </div>
    @endforelse
    @if($notifications->hasPages())
      <div>{{ $notifications->links() }}</div>
    @endif
  </div>
</div>
@endsection