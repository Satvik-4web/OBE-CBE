<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 text-xs font-semibold border border-indigo-500/20">
          <AppIcon name="courses" size="12" />
          <span>Criterion 2 & 3 • Course Definition & Outcomes</span>
        </div>
        <h3 class="text-xl font-bold text-white mt-1">
          Courses Catalog & Course Outcomes (COs) Management
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">
          Define accredited syllabus courses, specify Bloom taxonomy levels, and configure target attainment threshold levels (CO1 to CO6). Click any CO to view and edit its full statement.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button @click="openCreateCourseModal"
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 flex items-center gap-2 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add New Course
        </button>
      </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-slate-900/60 p-3 rounded-xl border border-slate-800">
      <!-- Semester Tabs -->
      <div class="flex items-center gap-1 overflow-x-auto pb-1 md:pb-0 scrollbar-thin">
        <button v-for="sem in [0, 1, 2, 3, 4, 5, 6, 7, 8]" :key="sem"
                @click="selectedSemester = sem"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition"
                :class="selectedSemester === sem ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-800'">
          {{ sem === 0 ? 'All Semesters (' + courses.length + ')' : `Sem ${sem}` }}
        </button>
      </div>

      <!-- Search Box -->
      <div class="relative w-full md:w-64">
        <input type="text" v-model="searchQuery" placeholder="Search course code or title..."
               class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:border-indigo-500 outline-none" />
        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>

    <!-- Courses Grid -->
    <div v-if="filteredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="course in filteredCourses" :key="course.id"
           class="glass-card rounded-xl p-5 border border-slate-800 flex flex-col justify-between hover:border-indigo-500/40 transition">
        <div>
          <div class="flex items-start justify-between gap-2">
            <div>
              <span class="text-xs font-mono font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20">
                {{ course.course_code }}
              </span>
              <span class="ml-2 text-[10px] text-slate-400">Sem {{ course.semester }} • {{ course.credits }} Credits</span>
            </div>
            <button @click="deleteCourse(course)" title="Delete Course" class="text-slate-500 hover:text-rose-400 transition text-xs">
              ✕
            </button>
          </div>

          <h4 class="text-sm font-bold text-white mt-2.5 leading-snug">
            {{ course.course_name }}
          </h4>

          <div class="mt-3 text-[11px] text-slate-400 flex items-center gap-2">
            <span>Hours: {{ course.lecture_hours }}L + {{ course.practical_hours }}P</span>
            <span>•</span>
            <span class="text-indigo-300 font-semibold">{{ course.course_outcomes?.length || 0 }} Course Outcomes</span>
          </div>

          <!-- CO List: Clickable to view & edit full CO -->
          <div class="mt-3 pt-3 border-t border-slate-800/80 space-y-1.5">
            <div v-for="co in course.course_outcomes" :key="co.id"
                 @click="openEditCoModal(course, co)"
                 title="Click to view full description and edit this CO"
                 class="flex items-center justify-between text-[11px] p-2 rounded-lg bg-slate-950/70 border border-slate-800/90 hover:border-indigo-500/60 hover:bg-indigo-950/30 cursor-pointer transition group shadow-sm">
              <div class="flex items-center gap-2 min-w-0 pr-2">
                <span class="font-mono font-bold text-indigo-400 group-hover:text-indigo-300 shrink-0">{{ co.co_code }}</span>
                <span class="text-[10px] text-slate-400 group-hover:text-slate-200 truncate" :title="co.description">
                  {{ co.description || 'No description provided' }}
                </span>
              </div>
              <div class="flex items-center gap-1.5 shrink-0">
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 group-hover:bg-indigo-900/60 text-slate-300 font-medium">{{ co.bloom_level }}</span>
                <span class="text-[10px] text-emerald-400 font-mono">T:{{ co.target_level }}</span>
                <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-indigo-400 transition ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </div>
            </div>

            <div v-if="!course.course_outcomes || course.course_outcomes.length === 0"
                 class="text-center py-2 text-[11px] text-slate-500 italic">
              No COs defined yet. Click below to add.
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
          <button @click="openAddCoModal(course)" class="text-indigo-400 hover:text-indigo-300 font-semibold text-[11px] flex items-center gap-1 transition">
            + Add CO Definition
          </button>
          <span class="text-[10px] text-slate-500">{{ course.course_type || 'Core' }}</span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-slate-900/40 rounded-2xl border border-slate-800">
      <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <h5 class="text-slate-300 font-semibold text-sm">No courses found matching your criteria</h5>
      <p class="text-slate-500 text-xs mt-1">Try selecting a different semester or clearing your search filter.</p>
      <button @click="selectedSemester = 0; searchQuery = ''" class="mt-3 px-3 py-1.5 rounded-lg bg-indigo-600/30 text-indigo-300 text-xs font-semibold hover:bg-indigo-600/50 transition">
        Reset Filters
      </button>
    </div>

    <!-- View & Edit CO Modal -->
    <div v-if="showEditCoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-md animate-fade-in" @click.self="showEditCoModal = false">
      <div class="glass-card bg-slate-900 border border-indigo-500/30 rounded-2xl w-full max-w-2xl p-6 space-y-5 shadow-2xl relative">
        <!-- Modal Header -->
        <div class="flex items-start justify-between border-b border-slate-800 pb-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/40">
                {{ editCoForm.co_code }}
              </span>
              <span class="text-xs text-slate-400 font-medium">
                {{ activeCourse?.course_code }} • {{ activeCourse?.course_name }} (Sem {{ activeCourse?.semester }})
              </span>
            </div>
            <h4 class="text-lg font-bold text-white mt-1.5 flex items-center gap-2">
              Course Outcome Details & Editor
            </h4>
          </div>
          <button @click="showEditCoModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Full CO Reading & Edit Form -->
        <form @submit.prevent="updateCo" class="space-y-4 text-xs">
          <!-- Full Statement Textarea -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between">
              <label class="font-semibold text-slate-200 text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Full Course Learning Outcome Statement (CO / CLO)
              </label>
              <span class="text-[11px] text-slate-500">{{ editCoForm.description?.length || 0 }} characters</span>
            </div>
            <p class="text-[11px] text-slate-400 leading-normal">
              Read and refine the complete pedagogical statement describing what students will demonstrate, compute, evaluate, or design upon completing this course outcome.
            </p>
            <textarea v-model="editCoForm.description" required rows="5"
                      placeholder="e.g. Understand damped and simple harmonic motion, the role of reverberation in designing a hall and generation and detection of ultrasonic waves..."
                      class="w-full bg-slate-950 border border-slate-700/80 rounded-xl p-3.5 text-sm text-slate-100 placeholder-slate-500 leading-relaxed focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition font-sans"></textarea>
          </div>

          <!-- Parameters Grid: CO Code, Bloom Taxonomy, Target Level, Target % -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
            <div>
              <label class="font-semibold text-slate-300 block mb-1">Outcome Code</label>
              <input type="text" v-model="editCoForm.co_code" required
                     placeholder="e.g. CO1"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono font-bold focus:border-indigo-500 outline-none" />
            </div>

            <div>
              <label class="font-semibold text-slate-300 block mb-1">Bloom Taxonomy</label>
              <select v-model="editCoForm.bloom_level" required
                      class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none">
                <option value="Remember">Remember (L1)</option>
                <option value="Understand">Understand (L2)</option>
                <option value="Apply">Apply (L3)</option>
                <option value="Analyze">Analyze (L4)</option>
                <option value="Evaluate">Evaluate (L5)</option>
                <option value="Create">Create (L6)</option>
              </select>
            </div>

            <div>
              <label class="font-semibold text-slate-300 block mb-1">Target Level (1.0 - 3.0)</label>
              <input type="number" step="0.1" min="1" max="3" v-model.number="editCoForm.target_level" required
                     placeholder="2.5"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-indigo-500 outline-none" />
            </div>

            <div>
              <label class="font-semibold text-slate-300 block mb-1">Target Pass % of Class</label>
              <div class="relative">
                <input type="number" min="1" max="100" v-model.number="editCoForm.target_percentage" required
                       placeholder="60"
                       class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-indigo-500 outline-none pr-7" />
                <span class="absolute right-2.5 top-2 text-slate-400 font-bold">%</span>
              </div>
            </div>
          </div>

          <!-- CO Context Note -->
          <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-xl p-3 flex items-center justify-between text-indigo-300 text-[11px]">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>Edits to this Course Outcome are saved instantly to the database and will update attainment tracking and the Articulation Matrix.</span>
            </div>
          </div>

          <!-- Modal Actions Footer -->
          <div class="flex items-center justify-between pt-3 border-t border-slate-800">
            <button type="button" @click="deleteCo(activeCourse, activeCo)"
                    class="px-3.5 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 font-semibold flex items-center gap-1.5 transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              Delete CO
            </button>

            <div class="flex items-center space-x-2.5">
              <button type="button" @click="showEditCoModal = false"
                      class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition">
                Cancel
              </button>
              <button type="submit" :disabled="saving"
                      class="px-5 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold shadow-lg shadow-indigo-600/30 flex items-center gap-2 transition">
                <svg v-if="saving" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ saving ? 'Saving Changes...' : 'Save CO Changes' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Create Course Modal -->
    <div v-if="showCourseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
      <div class="glass-card bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h4 class="text-sm font-bold text-white">Create New Course in Curriculum</h4>
          <button @click="showCourseModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form @submit.prevent="saveCourse" class="space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="font-semibold text-slate-300">Course Code</label>
              <input type="text" v-model="courseForm.course_code" required placeholder="e.g. UEC501"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
            <div>
              <label class="font-semibold text-slate-300">Semester (1 - 8)</label>
              <input type="number" v-model.number="courseForm.semester" required min="1" max="8"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
          </div>

          <div>
            <label class="font-semibold text-slate-300">Course Title / Name</label>
            <input type="text" v-model="courseForm.course_name" required placeholder="e.g. Embedded System Design"
                   class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
          </div>

          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="font-semibold text-slate-300">Credits</label>
              <input type="number" step="0.5" v-model.number="courseForm.credits" required
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
            <div>
              <label class="font-semibold text-slate-300">Lecture (L)</label>
              <input type="number" v-model.number="courseForm.lecture_hours"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
            <div>
              <label class="font-semibold text-slate-300">Practical (P)</label>
              <input type="number" v-model.number="courseForm.practical_hours"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-800">
            <button type="button" @click="showCourseModal = false" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300">Cancel</button>
            <button type="submit" :disabled="saving" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold">
              {{ saving ? 'Saving...' : 'Create Course' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Add CO Modal -->
    <div v-if="showCoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
      <div class="glass-card bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h4 class="text-sm font-bold text-white">Add CO to {{ activeCourse?.course_code }}</h4>
          <button @click="showCoModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form @submit.prevent="saveCo" class="space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="font-semibold text-slate-300">CO Code</label>
              <input type="text" v-model="coForm.co_code" required placeholder="e.g. CO6"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
            <div>
              <label class="font-semibold text-slate-300">Bloom Taxonomy</label>
              <select v-model="coForm.bloom_level" required
                      class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none">
                <option value="Remember">Remember</option>
                <option value="Understand">Understand</option>
                <option value="Apply">Apply</option>
                <option value="Analyze">Analyze</option>
                <option value="Evaluate">Evaluate</option>
                <option value="Create">Create</option>
              </select>
            </div>
          </div>

          <div>
            <label class="font-semibold text-slate-300">Course Outcome Description</label>
            <textarea v-model="coForm.description" required rows="3" placeholder="Demonstrate ability to..."
                      class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="font-semibold text-slate-300">Target Pass % (Students)</label>
              <input type="number" v-model.number="coForm.target_percentage" required placeholder="65"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
            <div>
              <label class="font-semibold text-slate-300">Target Level (1 - 3)</label>
              <input type="number" step="0.1" v-model.number="coForm.target_level" required placeholder="2.5"
                     class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-white focus:border-indigo-500 outline-none" />
            </div>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-800">
            <button type="button" @click="showCoModal = false" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300">Cancel</button>
            <button type="submit" :disabled="saving" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold">
              {{ saving ? 'Adding...' : 'Save CO Definition' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import AppIcon from './AppIcon.vue';

export default {
  name: 'CoursesManagementView',
  components: { AppIcon },
  props: {
    programId: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      courses: [],
      selectedSemester: 0,
      searchQuery: '',
      showCourseModal: false,
      showCoModal: false,
      showEditCoModal: false,
      activeCourse: null,
      activeCo: null,
      saving: false,
      courseForm: {
        program_id: 1,
        course_code: '',
        course_name: '',
        semester: 1,
        credits: 4.0,
        lecture_hours: 3,
        practical_hours: 2,
        course_type: 'Core',
      },
      coForm: {
        co_code: 'CO6',
        description: '',
        target_percentage: 65,
        target_level: 2.5,
        bloom_level: 'Apply',
      },
      editCoForm: {
        id: null,
        course_id: null,
        co_code: '',
        description: '',
        target_percentage: 65,
        target_level: 2.5,
        bloom_level: 'Apply',
      },
    };
  },
  computed: {
    filteredCourses() {
      let list = this.courses;
      if (this.selectedSemester > 0) {
        list = list.filter(c => c.semester === this.selectedSemester);
      }
      if (this.searchQuery && this.searchQuery.trim() !== '') {
        const q = this.searchQuery.toLowerCase().trim();
        list = list.filter(c =>
          (c.course_code && c.course_code.toLowerCase().includes(q)) ||
          (c.course_name && c.course_name.toLowerCase().includes(q))
        );
      }
      return list;
    },
  },
  mounted() {
    this.fetchCourses();
  },
  methods: {
    async fetchCourses() {
      try {
        const res = await axios.get('/api/v1/courses', {
          params: { program_id: this.programId },
        });
        if (res.data && res.data.success) {
          this.courses = res.data.data.courses || [];
        }
      } catch (err) {
        console.error('Failed to load courses:', err);
      }
    },
    openCreateCourseModal() {
      this.courseForm = {
        program_id: this.programId,
        course_code: 'UEC' + Math.floor(400 + Math.random() * 400),
        course_name: '',
        semester: this.selectedSemester > 0 ? this.selectedSemester : 1,
        credits: 4.0,
        lecture_hours: 3,
        practical_hours: 2,
        course_type: 'Core',
      };
      this.showCourseModal = true;
    },
    async saveCourse() {
      this.saving = true;
      try {
        await axios.post('/api/v1/courses', this.courseForm);
        this.showCourseModal = false;
        this.fetchCourses();
        this.$emit('updated');
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to create course');
      } finally {
        this.saving = false;
      }
    },
    openAddCoModal(course) {
      this.activeCourse = course;
      const count = (course.course_outcomes?.length || 0) + 1;
      this.coForm = {
        co_code: `CO${count}`,
        description: `Apply advanced engineering principles of ${course.course_name}.`,
        target_percentage: 65,
        target_level: 2.5,
        bloom_level: 'Analyze',
      };
      this.showCoModal = true;
    },
    async saveCo() {
      if (!this.activeCourse) return;
      this.saving = true;
      try {
        await axios.post(`/api/v1/courses/${this.activeCourse.id}/co`, this.coForm);
        this.showCoModal = false;
        this.fetchCourses();
        this.$emit('updated');
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to add CO');
      } finally {
        this.saving = false;
      }
    },
    openEditCoModal(course, co) {
      this.activeCourse = course;
      this.activeCo = co;
      this.editCoForm = {
        id: co.id,
        course_id: course.id,
        co_code: co.co_code,
        description: co.description,
        target_percentage: co.target_percentage ?? 65,
        target_level: co.target_level ?? 2.5,
        bloom_level: co.bloom_level || 'Apply',
      };
      this.showEditCoModal = true;
    },
    async updateCo() {
      if (!this.activeCourse || !this.activeCo) return;
      this.saving = true;
      try {
        const res = await axios.put(`/api/v1/courses/${this.activeCourse.id}/co/${this.activeCo.id}`, this.editCoForm);
        if (res.data && res.data.success) {
          Object.assign(this.activeCo, res.data.data);
          this.showEditCoModal = false;
          this.fetchCourses();
          this.$emit('updated');
        }
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to update Course Outcome');
      } finally {
        this.saving = false;
      }
    },
    async deleteCo(course, co) {
      if (!confirm(`Are you sure you want to delete ${co.co_code} from ${course.course_code}?`)) return;
      try {
        await axios.delete(`/api/v1/courses/${course.id}/co/${co.id}`);
        this.showEditCoModal = false;
        this.fetchCourses();
        this.$emit('updated');
      } catch (err) {
        alert(err.response?.data?.message || 'Failed to delete CO');
      }
    },
    async deleteCourse(course) {
      if (!confirm(`Are you sure you want to delete ${course.course_code}?`)) return;
      try {
        await axios.delete(`/api/v1/courses/${course.id}`);
        this.fetchCourses();
        this.$emit('updated');
      } catch (err) {
        alert(err.response?.data?.message || 'Delete failed');
      }
    },
  },
};
</script>
